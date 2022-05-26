<!doctype html>
<html lang=<?=$this->e($lang)?>>
    <head data-template-set="html5-reset" data-rel="<?php echo 'rel data'; ?>">
        <meta charset="utf-8">
        <title><?=$this->e($pageTitle)?></title>        
        <?=$this->section('head')?>   
    </head>
<body class=<?=$this->e($bodyClass)?>>

    <?=$this->section('content')?>
    <?=$this->section('scripts')?>

</body>
</html>