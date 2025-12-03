<?php $CURRENT = isset($CURRENT) ? $CURRENT : null; ?>
<?php $PREVIOUS_RELEASE = isset($PREVIOUS_RELEASE) ? $PREVIOUS_RELEASE : null; ?>
<?php $keepReleases = isset($keepReleases) ? $keepReleases : null; ?>
<?php $releaseDir = isset($releaseDir) ? $releaseDir : null; ?>
<?php $release = isset($release) ? $release : null; ?>
<?php $storageDir = isset($storageDir) ? $storageDir : null; ?>
<?php $sharedDir = isset($sharedDir) ? $sharedDir : null; ?>
<?php $currentDir = isset($currentDir) ? $currentDir : null; ?>
<?php $releasesDir = isset($releasesDir) ? $releasesDir : null; ?>
<?php $appDir = isset($appDir) ? $appDir : null; ?>
<?php $branch = isset($branch) ? $branch : null; ?>
<?php $repository = isset($repository) ? $repository : null; ?>
<?php $server = isset($server) ? $server : null; ?>
<?php
    // Configurazione server
    $server = 'production';
    $repository = 'git@github.com:username/gestionale-cantieri.git'; // Sostituisci con il tuo repository
    $branch = 'main';

    // Path sul server
    $appDir = '/var/www/gestionale-cantieri';
    $releasesDir = $appDir . '/releases';
    $currentDir = $appDir . '/current';
    $sharedDir = $appDir . '/shared';
    $storageDir = $sharedDir . '/storage';

    // Nome della release (timestamp)
    $release = date('YmdHis');
    $releaseDir = $releasesDir . '/' . $release;

    // Numero di release da mantenere
    $keepReleases = 5;
?>

<?php $__container->servers(['production' => 'user@your-server.com']); ?> <?php /* Sostituisci con il tuo server */ ?>

<?php /* Task principale di deploy */ ?>
<?php $__container->startMacro('deploy'); ?>
    git-clone
    install-dependencies
    link-shared
    optimize
    migrate
    activate-release
    cleanup
    restart-services
<?php $__container->endMacro(); ?>

<?php /* Clone del repository */ ?>
<?php $__container->startTask('git-clone'); ?>
    echo "🔄 Cloning repository..."

    [ -d <?php echo $releasesDir; ?> ] || mkdir -p <?php echo $releasesDir; ?>

    cd <?php echo $releasesDir; ?>


    git clone --depth 1 --branch <?php echo $branch; ?> <?php echo $repository; ?> <?php echo $release; ?>

    cd <?php echo $releaseDir; ?>


    echo "✅ Repository cloned!"
<?php $__container->endTask(); ?>

<?php /* Installazione dipendenze */ ?>
<?php $__container->startTask('install-dependencies'); ?>
    echo "📦 Installing dependencies..."

    cd <?php echo $releaseDir; ?>


    # Composer
    composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

    # NPM (se necessario)
    # npm ci --production
    # npm run build

    echo "✅ Dependencies installed!"
<?php $__container->endTask(); ?>

<?php /* Link delle directory shared */ ?>
<?php $__container->startTask('link-shared'); ?>
    echo "🔗 Linking shared directories..."

    # Crea directory shared se non esistono
    [ -d <?php echo $sharedDir; ?> ] || mkdir -p <?php echo $sharedDir; ?>

    [ -d <?php echo $storageDir; ?> ] || mkdir -p <?php echo $storageDir; ?>

    [ -d <?php echo $storageDir; ?>/app ] || mkdir -p <?php echo $storageDir; ?>/app
    [ -d <?php echo $storageDir; ?>/framework ] || mkdir -p <?php echo $storageDir; ?>/framework
    [ -d <?php echo $storageDir; ?>/logs ] || mkdir -p <?php echo $storageDir; ?>/logs

    # Copia .env se non esiste
    if [ ! -f <?php echo $sharedDir; ?>/.env ]; then
        echo "⚠️  Creating .env file..."
        cp <?php echo $releaseDir; ?>/.env.example <?php echo $sharedDir; ?>/.env
        echo "⚠️  Remember to configure <?php echo $sharedDir; ?>/.env!"
    fi

    # Rimuovi e crea symlink
    rm -rf <?php echo $releaseDir; ?>/storage
    ln -nfs <?php echo $storageDir; ?> <?php echo $releaseDir; ?>/storage

    rm -f <?php echo $releaseDir; ?>/.env
    ln -nfs <?php echo $sharedDir; ?>/.env <?php echo $releaseDir; ?>/.env

    # Permessi storage
    chmod -R 775 <?php echo $storageDir; ?>


    echo "✅ Shared directories linked!"
<?php $__container->endTask(); ?>

<?php /* Ottimizzazione Laravel */ ?>
<?php $__container->startTask('optimize'); ?>
    echo "⚡ Optimizing application..."

    cd <?php echo $releaseDir; ?>


    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache

    echo "✅ Application optimized!"
<?php $__container->endTask(); ?>

<?php /* Esegui migrazioni */ ?>
<?php $__container->startTask('migrate'); ?>
    echo "🗄️  Running migrations..."

    cd <?php echo $releaseDir; ?>

    php artisan migrate --force

    echo "✅ Migrations completed!"
<?php $__container->endTask(); ?>

<?php /* Attiva la nuova release */ ?>
<?php $__container->startTask('activate-release'); ?>
    echo "🚀 Activating new release..."

    ln -nfs <?php echo $releaseDir; ?> <?php echo $currentDir; ?>


    echo "✅ Release <?php echo $release; ?> activated!"
<?php $__container->endTask(); ?>

<?php /* Pulizia vecchie release */ ?>
<?php $__container->startTask('cleanup'); ?>
    echo "🧹 Cleaning up old releases..."

    cd <?php echo $releasesDir; ?>

    ls -t | tail -n +<?php echo $keepReleases + 1; ?> | xargs -r rm -rf

    echo "✅ Old releases cleaned!"
<?php $__container->endTask(); ?>

<?php /* Restart servizi */ ?>
<?php $__container->startTask('restart-services'); ?>
    echo "🔄 Restarting services..."

    # PHP-FPM
    sudo systemctl restart php8.2-fpm

    # Nginx
    sudo systemctl reload nginx

    # Queue worker (se usi le code)
    # php artisan queue:restart

    echo "✅ Services restarted!"
<?php $__container->endTask(); ?>

<?php /* Rollback all'ultima release */ ?>
<?php $__container->startTask('rollback'); ?>
    echo "⏪ Rolling back to previous release..."

    cd <?php echo $releasesDir; ?>

    PREVIOUS_RELEASE=$(ls -t | sed -n '2p')

    if [ -z "$PREVIOUS_RELEASE" ]; then
        echo "❌ No previous release found!"
        exit 1
    fi

    echo "Rolling back to: $PREVIOUS_RELEASE"
    ln -nfs <?php echo $releasesDir; ?>/$PREVIOUS_RELEASE <?php echo $currentDir; ?>


    cd <?php echo $releasesDir; ?>/$PREVIOUS_RELEASE
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    sudo systemctl restart php8.2-fpm
    sudo systemctl reload nginx

    echo "✅ Rolled back to: $PREVIOUS_RELEASE"
<?php $__container->endTask(); ?>

<?php /* Task di setup iniziale del server */ ?>
<?php $__container->startTask('setup'); ?>
    echo "🔧 Setting up application structure..."

    # Crea le directory necessarie
    mkdir -p <?php echo $appDir; ?>

    mkdir -p <?php echo $releasesDir; ?>

    mkdir -p <?php echo $sharedDir; ?>

    mkdir -p <?php echo $storageDir; ?>/app/public
    mkdir -p <?php echo $storageDir; ?>/framework/cache
    mkdir -p <?php echo $storageDir; ?>/framework/sessions
    mkdir -p <?php echo $storageDir; ?>/framework/views
    mkdir -p <?php echo $storageDir; ?>/logs

    # Copia .env.example se non esiste .env
    if [ ! -f <?php echo $sharedDir; ?>/.env ]; then
        echo "Creating .env from .env.example..."
        cd <?php echo $releasesDir; ?>

        git clone --depth 1 --branch <?php echo $branch; ?> <?php echo $repository; ?> temp
        cp temp/.env.example <?php echo $sharedDir; ?>/.env
        rm -rf temp
    fi

    # Permessi
    chmod -R 775 <?php echo $storageDir; ?>


    echo "✅ Application structure created!"
    echo "⚠️  Don't forget to configure <?php echo $sharedDir; ?>/.env"
    echo "⚠️  Then run: envoy run deploy"
<?php $__container->endTask(); ?>

<?php /* Task per vedere lo stato */ ?>
<?php $__container->startTask('status'); ?>
    echo "📊 Application Status"
    echo "===================="

    if [ -L <?php echo $currentDir; ?> ]; then
        CURRENT=$(readlink <?php echo $currentDir; ?>)
        echo "Current release: $(basename $CURRENT)"
    else
        echo "No active release"
    fi

    echo ""
    echo "Available releases:"
    ls -lt <?php echo $releasesDir; ?> | grep ^d | awk '{print $9}' | head -5

    echo ""
    echo "PHP-FPM status:"
    sudo systemctl status php8.2-fpm --no-pager | head -3

    echo ""
    echo "Nginx status:"
    sudo systemctl status nginx --no-pager | head -3
<?php $__container->endTask(); ?>

<?php /* Task per i log */ ?>
<?php $__container->startTask('logs'); ?>
    echo "📋 Latest logs (last 50 lines):"
    tail -n 50 <?php echo $storageDir; ?>/logs/laravel.log
<?php $__container->endTask(); ?>

<?php /* Notifiche */ ?>
<?php $_vars = get_defined_vars(); $__container->after(function($task) use ($_vars) { extract($_vars, EXTR_SKIP)  ; 
    echo "✨ Deploy completed successfully!"
    echo "🌐 Application is now live!"
}); ?>

<?php $_vars = get_defined_vars(); $__container->error(function($task) use ($_vars) { extract($_vars, EXTR_SKIP); 
    echo "❌ Deploy failed!"
    echo "Check the errors above for details."
}); ?>

