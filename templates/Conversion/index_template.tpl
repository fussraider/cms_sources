<!doctype html>
<html>
<head>
	<meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<title>{:PAGE_TITLE:} - {:SITE_NAME:}</title>
	<link rel="stylesheet" type="text/css" media="all" href="/templates/Conversion/styles/main.css"/>
    <link rel="stylesheet" type="text/css" media="screen and (min-width: 1440px)" href="/templates/Conversion/styles/large-screen.css">
    <link rel="stylesheet" type="text/css" media="screen and (max-width: 1440px) and (min-width: 980px)" href="/templates/Conversion/styles/middle-screen.css">
    <link rel="stylesheet" type="text/css" media="screen and (max-width : 980px ) and (min-width : 588px)" href="/templates/Conversion/styles/small-screen.css">
    <link rel="stylesheet" type="text/css" media="screen and (max-width: 640px)" href="/templates/Conversion/styles/smallest-screen.css">
    <script type="text/javascript" src="/templates/Conversion/js/jquery.min.js"></script>    
    <script type="text/javascript" src="/templates/Conversion/js/masonry.pkgd.min.js"></script>
    <script type="text/javascript" src="/templates/Conversion/js/imagesloaded.pkgd.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var $container = $('.posts');
            $container.imagesLoaded(function(){
            $container.masonry({ 
                    itemSelector: '.item',
                    columnWidth: ".item",
                    singleMode: true,
                    resizeable: true
                })
            });

            $(window).resize(function() {
                $container.masonry('reloadItems');
                $container.masonry('layout');
            });
        });
    </script>
</head>

<body>
    <div class="top-bar">
        <div id="motto">Minimalism. Atheism. Whiskey!</div>
    </div>
    {*HEADER*}
	{*MENU*}
    {*INTERESTING*}
   	<div>
    {*CONTENT*}
    </div>
    {*FOOTER*}
</body>
</html>