ElnurValidatorBundle
====================

Installation
------------

1.  Add this to the `deps` file:

        [ElnurValidatorBundle]
            git=http://github.com/elnur/ElnurValidatorBundle.git
            target=/bundles/Elnur/ValidatorBundle

    And run `bin/vendors install`.

2.  Register the `Elnur` namespace in the `app/autoload.php` file:

        $loader->registerNamespaces(array(
            // ...
            'Elnur'            => __DIR__.'/../vendor/bundles',
        ));

3.  Register the bundle in the `app/AppKernel.php` file:

        public function registerBundles()
        {
            $bundles = array(
                // ...
                new Elnur\ValidatorBundle\ElnurValidatorBundle(),
            );
        }

License
-------

This bundle is under the MIT license. See the complete license in the bundle:

    Resources/meta/LICENSE
