# PHP Obfuscator

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/naneau/php-obfuscator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/naneau/php-obfuscator/?branch=master)

This is an "obfuscator" for PSR/OOp PHP code. Different from other obfuscators, which often use a (reversible) `eval()` based obfuscation, this tool actually [parses PHP](https://github.com/nikic/PHP-Parser), and obfuscates variable names, methods, etc. This means is can not be reversed by tools such as [UnPHP](http://www.unphp.net).

This library was written out of the need to obfuscate the source for a private library which for various reasons could not be shared without steps to protect the source from prying eyes. It is not technically feasible to "encrypt" PHP source code, while retaining the option to run it on a standard PHP runtime. Tools such as [Zend Guard](http://www.zend.com/products/guard) use run-time plugins, but even these offer no real security.

While this tool does not make PHP code impossible to read, it will make it significantly less legible.

It is compatible with PHP 5.2 - 7.0, but needs PHP 5.4+ to run.

## Usage

After cloning this repository (`git clone https://github.com/naneau/php-obfuscator`) and installing the dependencies through Composer (`composer install`), run the following command to obfuscate a directory of PHP files:

```bash
./bin/obfuscate obfuscate /input/directory /output/directory
```

If you've installed this package through [Composer](https://getcomposer.org), you'll find the `obfuscate` command in the relevant [bin dir](https://getcomposer.org/doc/articles/vendor-binaries.md).

### Configuration

You may find that you'll need to prevent certain variables and methods from being renamed, or to disable certain features. In this case you can create a simple YAML configuration file:

```yaml
parameters:

    # Ignore variable names
    obfuscator.scramble_variable.ignore:
        - foo
        - bar
        - baz

    # Ignore certain methods names
    obfuscator.scramble_private_method.ignore:
        - foo
        - bar
        - baz

    # Ignore certain private property names
    obfuscator.scramble_private_property.ignore: []

    # Ignore certain use statements
    obfuscator.scramble_use.ignore: []

    # When using A\B\C, but referring to C\D, add a use statement for hiding
    # C\D. Otherwise, it will be X\D, where X is an alias.
    obfuscator.scramble_use.add_as_use: true

    # Do not reuse a reference to a use statement twice. In other words, given
    # A\B\C and a second A\B\C, it will resolve to X and Y instead of X and X.
    obfuscator.scramble_use.do_not_reuse: false

    # Scramble comments
    obfuscator.remove_comments.preserve_annotations: true

    # Annotations flavor. Generic is the most common type of annotation syntax.
    obfuscator.remove_comments.annotations_flavor: "generic"

    # Output printer (set to Naneau\Obfuscator\PrettyPrinter\Stripping to
    # strip white spaces, or just pass the `leave_whitespace' option).
    obfuscator.printer: PhpParser\PrettyPrinter\Standard

    # PHP version (1 = Prefer PHP7, 2 = Prefer PHP5, 3 = PHP7, 4 = PHP5)
    obfuscator.language: 1
```

You can run the obfuscator with a configuration file through

```bash
./bin/obfuscate obfuscate /input/directory /output/directory --config=/foo/bar/config.yml
```

### Samples

The `samples/` directory contains some example code. They have been generated using default configuration, but white spaces are left in place for clarity. The samples only demonstrate the obfuscation techniques, thus may not be runnable.

### Limitations

PHP is not a statically typed language. This means that, in many cases, it is impossible to infer the type of an expression. Without this information, it is hard to tell whether a variable, methods or class can be safely renamed or not. Therefore, only private members are renamed, because this obfuscator cannot tell/predict whether a protected or public method is/will be used outside the current file.

Where possible, basic scope tracking is used to infer types of variables. Wherever this doesn't work, you are suggested to ignore variables and/or methods.

Code that is using methods such as `call_user_func[_array]` or `is_callable` on private members will not work without ignoring these members.

For an example, see `samples/private_public_methods.in.php`.
