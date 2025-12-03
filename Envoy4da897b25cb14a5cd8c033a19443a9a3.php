<?php $CURRENT = isset($CURRENT) ? $CURRENT : null; ?>
<?php $PREVIOUS = isset($PREVIOUS) ? $PREVIOUS : null; ?>
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
    $server = 'production';
    $repository = 'git@github.com:username/gestionale-cantieri.git';
    $branch = 'main';
    $appDir = '/var/www/gestionale-cantieri';
    $releasesDir = $appDir . '/releases';
    $currentDir = $appDir . '/current';
    $sharedDir = $appDir . '/shared';
    $storageDir = $sharedDir . '/storage';
    $release = date('YmdHis');
    $releaseDir = $releasesDir . '/' . $release;
    $keepReleases = 5;
?>
<?php $__container->servers(['production' => 'user@your-server.com']); ?>
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
<?php $__container->startTask('git-clone'); ?>
    echo "🔄 Cloning repository..."
    [ -d <?php echo $releasesDir; ?> ] || mkdir -p <?php echo $releasesDir; ?>

    cd <?php echo $releasesDir; ?>

    git clone --depth 1 --branch <?php echo $branch; ?> <?php echo $repository; ?> <?php echo $release; ?>

    cd <?php echo $releaseDir; ?>

    echo "✅ Repository cloned!"
<?php $__container->endTask(); ?>
<?php $__container->startTask('install-dependencies'); ?>
    echo "📦 Installing dependencies..."
    cd <?php echo $releaseDir; ?>

    composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
    echo "✅ Dependencies installed!"
<?php $__container->endTask(); ?>
<?php $__container->startTask('link-shared'); ?>
    echo "🔗 Linking shared directories..."
    [ -d <?php echo $sharedDir; ?> ] || mkdir -p <?php echo $sharedDir; ?>

    [ -d <?php echo $storageDir; ?> ] || mkdir -p <?php echo $storageDir; ?>

    [ -d <?php echo $storageDir; ?>/app ] || mkdir -p <?php echo $storageDir; ?>/app
    [ -d <?php echo $storageDir; ?>/framework ] || mkdir -p <?php echo $storageDir; ?>/framework
    [ -d <?php echo $storageDir; ?>/logs ] || mkdir -p <?php echo $storageDir; ?>/logs
    if [ ! -f <?php echo $sharedDir; ?>/.env ]; then
        echo "⚠️  Creating .env file..."
        cp <?php echo $releaseDir; ?>/.env.example <?php echo $sharedDir; ?>/.env
        echo "⚠️  Remember to configure <?php echo $sharedDir; ?>/.env!"
    fi
    rm -rf <?php echo $releaseDir; ?>/storage
    ln -nfs <?php echo $storageDir; ?> <?php echo $releaseDir; ?>/storage
    rm -f <?php echo $releaseDir; ?>/.env
    ln -nfs <?php echo $sharedDir; ?>/.env <?php echo $releaseDir; ?>/.env
    chmod -R 775 <?php echo $storageDir; ?>

    echo "✅ Shared directories linked!"
<?php $__container->endTask(); ?>
<?php $__container->startTask('optimize'); ?>
    echo "⚡ Optimizing application..."
    cd <?php echo $releaseDir; ?>

    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
    echo "✅ Application optimized!"
<?php $__container->endTask(); ?>
<?php $__container->startTask('migrate'); ?>
    echo "🗄️  Running migrations..."
    cd <?php echo $releaseDir; ?>

    php artisan migrate --force
    echo "✅ Migrations completed!"
<?php $__container->endTask(); ?>
<?php $__container->startTask('activate-release'); ?>
    echo "🚀 Activating new release..."
    ln -nfs <?php echo $releaseDir; ?> <?php echo $currentDir; ?>

    echo "✅ Release <?php echo $release; ?> activated!"
<?php $__container->endTask(); ?>
<?php $__container->startTask('cleanup'); ?>
    echo "🧹 Cleaning up old releases..."
    cd <?php echo $releasesDir; ?>

    ls -t | tail -n +<?php echo $keepReleases + 1; ?> | xargs -r rm -rf
    echo "✅ Old releases cleaned!"
<?php $__container->endTask(); ?>
<?php $__container->startTask('restart-services'); ?>
    echo "🔄 Restarting services..."
    sudo systemctl restart php8.2-fpm
    sudo systemctl reload nginx
    echo "✅ Services restarted!"
<?php $__container->endTask(); ?>
<?php $__container->startTask('rollback'); ?>
    echo "⏪ Rolling back to previous release..."
    cd <?php echo $releasesDir; ?>

    PREVIOUS=$(ls -t | sed -n '2p')
    if [ -z "$PREVIOUS" ]; then
        echo "❌ No previous release found!"
        exit 1
    fi
    echo "Rolling back to: $PREVIOUS"
    ln -nfs <?php echo $releasesDir; ?>/$PREVIOUS <?php echo $currentDir; ?>

    cd <?php echo $releasesDir; ?>/$PREVIOUS
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    sudo systemctl restart php8.2-fpm
    sudo systemctl reload nginx
    echo "✅ Rolled back to: $PREVIOUS"
<?php $__container->endTask(); ?>
<?php $__container->startTask('setup'); ?>
    echo "🔧 Setting up application structure..."
    mkdir -p <?php echo $appDir; ?>

    mkdir -p <?php echo $releasesDir; ?>

    mkdir -p <?php echo $sharedDir; ?>

    mkdir -p <?php echo $storageDir; ?>/app/public
    mkdir -p <?php echo $storageDir; ?>/framework/cache
    mkdir -p <?php echo $storageDir; ?>/framework/sessions
    mkdir -p <?php echo $storageDir; ?>/framework/views
    mkdir -p <?php echo $storageDir; ?>/logs
    chmod -R 775 <?php echo $storageDir; ?>

    echo "✅ Application structure created!"
    echo "⚠️  Configure <?php echo $sharedDir; ?>/.env and run: envoy run deploy"
<?php $__container->endTask(); ?>
<?php $__container->startTask('status'); ?>
    echo "📊 Application Status"
    if [ -L <?php echo $currentDir; ?> ]; then
        CURRENT=$(readlink <?php echo $currentDir; ?>)
        echo "Current release: $(basename $CURRENT)"
    else
        echo "No active release"
    fi
    echo ""
    echo "Available releases:"
    ls -lt <?php echo $releasesDir; ?> | grep ^d | awk '{print $9}' | head -5
<?php $__container->endTask(); ?>
<?php $__container->startTask('logs'); ?>
    echo "📋 Latest logs:"
    tail -n 50 <?php echo $storageDir; ?>/logs/laravel.log
<?php $__container->endTask(); ?>
<?php $_vars = get_defined_vars(); $__container->after(function($task) use ($_vars) { extract($_vars, EXTR_SKIP)  ; 
    echo "✨ Task completed successfully!"
}); ?>
<?php $_vars = get_defined_vars(); $__container->error(function($task) use ($_vars) { extract($_vars, EXTR_SKIP); 
    echo "❌ Task failed!"
}); ?>
