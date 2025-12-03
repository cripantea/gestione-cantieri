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
<?php
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
    clone
    dependencies
    link
    optimize
    migrate
    activate
    cleanup
    restart
<?php $__container->endMacro(); ?>
<?php $__container->startTask('clone'); ?>
    echo "Cloning repository..."
    [ -d <?php echo $releasesDir; ?> ] || mkdir -p <?php echo $releasesDir; ?>

    cd <?php echo $releasesDir; ?>

    git clone --depth 1 --branch <?php echo $branch; ?> <?php echo $repository; ?> <?php echo $release; ?>

    echo "Repository cloned"
<?php $__container->endTask(); ?>
<?php $__container->startTask('dependencies'); ?>
    echo "Installing dependencies..."
    cd <?php echo $releaseDir; ?>

    composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
    echo "Dependencies installed"
<?php $__container->endTask(); ?>
<?php $__container->startTask('link'); ?>
    echo "Linking shared directories..."
    [ -d <?php echo $sharedDir; ?> ] || mkdir -p <?php echo $sharedDir; ?>

    [ -d <?php echo $storageDir; ?> ] || mkdir -p <?php echo $storageDir; ?>

    [ -d <?php echo $storageDir; ?>/app ] || mkdir -p <?php echo $storageDir; ?>/app
    [ -d <?php echo $storageDir; ?>/framework ] || mkdir -p <?php echo $storageDir; ?>/framework
    [ -d <?php echo $storageDir; ?>/logs ] || mkdir -p <?php echo $storageDir; ?>/logs
    if [ ! -f <?php echo $sharedDir; ?>/.env ]; then
        cp <?php echo $releaseDir; ?>/.env.example <?php echo $sharedDir; ?>/.env
    fi
    rm -rf <?php echo $releaseDir; ?>/storage
    ln -nfs <?php echo $storageDir; ?> <?php echo $releaseDir; ?>/storage
    rm -f <?php echo $releaseDir; ?>/.env
    ln -nfs <?php echo $sharedDir; ?>/.env <?php echo $releaseDir; ?>/.env
    chmod -R 775 <?php echo $storageDir; ?>

    echo "Directories linked"
<?php $__container->endTask(); ?>
<?php $__container->startTask('optimize'); ?>
    echo "Optimizing application..."
    cd <?php echo $releaseDir; ?>

    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    echo "Application optimized"
<?php $__container->endTask(); ?>
<?php $__container->startTask('migrate'); ?>
    echo "Running migrations..."
    cd <?php echo $releaseDir; ?>

    php artisan migrate --force
    echo "Migrations completed"
<?php $__container->endTask(); ?>
<?php $__container->startTask('activate'); ?>
    echo "Activating release..."
    ln -nfs <?php echo $releaseDir; ?> <?php echo $currentDir; ?>

    echo "Release activated"
<?php $__container->endTask(); ?>
<?php $__container->startTask('cleanup'); ?>
    echo "Cleaning old releases..."
    cd <?php echo $releasesDir; ?>

    ls -t | tail -n +<?php echo $keepReleases + 1; ?> | xargs -r rm -rf
    echo "Cleanup done"
<?php $__container->endTask(); ?>
<?php $__container->startTask('restart'); ?>
    echo "Restarting services..."
    sudo systemctl restart php8.2-fpm
    sudo systemctl reload nginx
    echo "Services restarted"
<?php $__container->endTask(); ?>
<?php $__container->startTask('rollback'); ?>
    echo "Rolling back..."
    cd <?php echo $releasesDir; ?>

    PREVIOUS=$(ls -t | sed -n '2p')
    [ -z "$PREVIOUS" ] && echo "No previous release" && exit 1
    ln -nfs <?php echo $releasesDir; ?>/$PREVIOUS <?php echo $currentDir; ?>

    cd <?php echo $releasesDir; ?>/$PREVIOUS
    php artisan config:cache
    sudo systemctl restart php8.2-fpm
    echo "Rolled back to $PREVIOUS"
<?php $__container->endTask(); ?>
<?php $__container->startTask('setup'); ?>
    echo "Setting up structure..."
    mkdir -p <?php echo $appDir; ?> <?php echo $releasesDir; ?> <?php echo $sharedDir; ?>

    mkdir -p <?php echo $storageDir; ?>/app/public
    mkdir -p <?php echo $storageDir; ?>/framework/cache
    mkdir -p <?php echo $storageDir; ?>/framework/sessions
    mkdir -p <?php echo $storageDir; ?>/framework/views
    mkdir -p <?php echo $storageDir; ?>/logs
    chmod -R 775 <?php echo $storageDir; ?>

    echo "Structure created"
<?php $__container->endTask(); ?>
<?php $__container->startTask('status'); ?>
    echo "=== Status ==="
    if [ -L <?php echo $currentDir; ?> ]; then
        CURRENT=$(readlink <?php echo $currentDir; ?>)
        echo "Current: $(basename $CURRENT)"
    fi
    echo "Releases:"
    ls -lt <?php echo $releasesDir; ?> 2>/dev/null | grep ^d | awk '{print $9}' | head -5
<?php $__container->endTask(); ?>
<?php $__container->startTask('logs'); ?>
    tail -n 50 <?php echo $storageDir; ?>/logs/laravel.log
<?php $__container->endTask(); ?>
<?php $_vars = get_defined_vars(); $__container->after(function($task) use ($_vars) { extract($_vars, EXTR_SKIP)  ; 
    echo "=== Task completed ==="
}); ?>
<?php $_vars = get_defined_vars(); $__container->error(function($task) use ($_vars) { extract($_vars, EXTR_SKIP); 
    echo "=== Task failed ==="
}); ?>
