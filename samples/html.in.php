<!DOCTYPE html>
<html>
    <head><?php echo "HelloWorld"; ?></head>
    <body><?php $foo = ["bar" => "baz"]; echo (new Template($foo))->render(); ?></body>
</html>
