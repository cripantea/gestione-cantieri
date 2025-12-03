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
?>

<?php $__container->servers(['production' => 'user@your-server.com']); ?>

<?php $__container->startMacro('deploy'); ?>
    clone
    dependencies
    link-shared
    optimize
    migrate
    activate
    cleanup
    restart
<?php $__container->endMacro(); ?>

<?php $__container->startTask('clone', ['on' => 'production']); ?>
    echo "Cloning repository..."
    mkdir -p <?php echo $releasesDir; ?>

    cd <?php echo $releasesDir; ?>

    git clone --depth 1 --branch <?php echo $branch; ?> <?php echo $repository; ?> <?php echo $release; ?>

    echo "Done"
<?php $__container->endTask(); ?>

<?php $__container->startTask('dependencies', ['on' => 'production']); ?>
    echo "Installing dependencies..."
    cd <?php echo $releaseDir; ?>

    composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
    echo "Done"
<?php $__container->endTask(); ?>

<?php $__container->startTask('link-shared', ['on' => 'production']); ?>
    echo "Linking shared..."
    mkdir -p <?php echo $sharedDir; ?>

    mkdir -p <?php echo $storageDir; ?>/app
    mkdir -p <?php echo $storageDir; ?>/framework
    mkdir -p <?php echo $storageDir; ?>/logs
    rm -rf <?php echo $releaseDir; ?>/storage
    ln -nfs <?php echo $storageDir; ?> <?php echo $releaseDir; ?>/storage
    rm -f <?php echo $releaseDir; ?>/.env
    ln -nfs <?php echo $sharedDir; ?>/.env <?php echo $releaseDir; ?>/.env
    chmod -R 775 <?php echo $storageDir; ?>

    echo "Done"
<?php $__container->endTask(); ?>

<?php $__container->startTask('optimize', ['on' => 'production']); ?>
    cd <?php echo $releaseDir; ?>

    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
<?php $__container->endTask(); ?>

<?php $__container->startTask('migrate', ['on' => 'production']); ?>
    cd <?php echo $releaseDir; ?>

    php artisan migrate --force
<?php $__container->endTask(); ?>

<?php $__container->startTask('activate', ['on' => 'production']); ?>
    ln -nfs <?php echo $releaseDir; ?> <?php echo $currentDir; ?>

    echo "Release activated"
<?php $__container->endTask(); ?>

<?php $__container->startTask('cleanup', ['on' => 'production']); ?>
    cd <?php echo $releasesDir; ?>

    ls -t | tail -n +6 | xargs rm -rf
<?php $__container->endTask(); ?>

<?php $__container->startTask('restart', ['on' => 'production']); ?>
    sudo systemctl restart php8.2-fpm
    sudo systemctl reload nginx
<?php $__container->endTask(); ?>

<?php $__container->startTask('rollback', ['on' => 'production']); ?>
    cd <?php echo $releasesDir; ?>

    ln -nfs $(ls -t | sed -n '2p') <?php echo $currentDir; ?>

    cd <?php echo $currentDir; ?>

    php artisan config:cache
    sudo systemctl restart php8.2-fpm
<?php $__container->endTask(); ?>

<?php $__container->startTask('status', ['on' => 'production']); ?>
    echo "Current release:"
    readlink <?php echo $currentDir; ?>

    echo "Available releases:"
    ls -lt <?php echo $releasesDir; ?>

<?php $__container->endTask(); ?>

<?php $_vars = get_defined_vars(); $__container->after(function($task) use ($_vars) { extract($_vars, EXTR_SKIP)  ; 
    echo "Task completed!"
}); ?>

