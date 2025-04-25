<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    'demo' => [
        'path' => './assets/demo.js',
        'entrypoint' => true,
    ],
    'security' => [
        'path' => './assets/security.js',
        'entrypoint' => true,
    ],
    'utilisateur' => [
        'path' => './assets/utilisateur.js',
        'entrypoint' => true,
    ],
    'actualite' => [
        'path' => './assets/actualite.js',
        'entrypoint' => true,
    ],
    'article' => [
        'path' => './assets/article.js',
        'entrypoint' => true,
    ],
    'categorie' => [
        'path' => './assets/categorie.js',
        'entrypoint' => true,
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    'bootstrap' => [
        'version' => '5.3.3',
    ],
    '@popperjs/core' => [
        'version' => '2.11.8',
    ],
    'bootstrap/dist/css/bootstrap.min.css' => [
        'version' => '5.3.3',
        'type' => 'css',
    ],
    'jquery' => [
        'version' => '3.7.1',
    ],
    'bloodhound-js' => [
        'version' => '1.2.3',
    ],
    'object-assign' => [
        'version' => '4.1.1',
    ],
    'es6-promise' => [
        'version' => '4.2.8',
    ],
    'storage2' => [
        'version' => '0.1.2',
    ],
    'superagent' => [
        'version' => '10.1.1',
    ],
    'component-emitter' => [
        'version' => '2.0.0',
    ],
    'typeahead.js' => [
        'version' => '0.11.1',
    ],
    '@fortawesome/fontawesome-free/css/all.css' => [
        'version' => '6.7.2',
        'type' => 'css',
    ],
    '@fortawesome/fontawesome-free/css/v4-shims.css' => [
        'version' => '6.7.2',
        'type' => 'css',
    ],
    'fast-safe-stringify' => [
        'version' => '2.1.1',
    ],
    'qs' => [
        'version' => '6.14.0',
    ],
    'side-channel' => [
        'version' => '1.1.0',
    ],
    'get-intrinsic' => [
        'version' => '1.2.7',
    ],
    'call-bind/callBound' => [
        'version' => '1.0.8',
    ],
    'object-inspect' => [
        'version' => '1.13.3',
    ],
    'has-symbols' => [
        'version' => '1.1.0',
    ],
    'has-proto' => [
        'version' => '1.2.0',
    ],
    'function-bind' => [
        'version' => '1.1.2',
    ],
    'hasown' => [
        'version' => '2.0.2',
    ],
    'set-function-length' => [
        'version' => '1.2.2',
    ],
    'define-data-property' => [
        'version' => '1.1.4',
    ],
    'has-property-descriptors' => [
        'version' => '1.0.2',
    ],
    'gopd' => [
        'version' => '1.2.0',
    ],
    'select2' => [
        'version' => '4.1.0-rc.0',
    ],
    'select2/dist/css/select2.min.css' => [
        'version' => '4.1.0-rc.0',
        'type' => 'css',
    ],
    'zxcvbn' => [
        'version' => '4.4.2',
    ],
    '@canvasjs/charts' => [
        'version' => '3.11.0',
    ],
    'tom-select' => [
        'version' => '2.4.1',
    ],
    'tom-select/dist/css/tom-select.bootstrap5.css' => [
        'version' => '2.4.1',
        'type' => 'css',
    ],
    'es-errors/type' => [
        'version' => '1.3.0',
    ],
    'es-errors' => [
        'version' => '1.3.0',
    ],
    'es-errors/eval' => [
        'version' => '1.3.0',
    ],
    'es-errors/range' => [
        'version' => '1.3.0',
    ],
    'es-errors/ref' => [
        'version' => '1.3.0',
    ],
    'es-errors/syntax' => [
        'version' => '1.3.0',
    ],
    'es-errors/uri' => [
        'version' => '1.3.0',
    ],
    'es-define-property' => [
        'version' => '1.0.1',
    ],
    '@hotwired/turbo' => [
        'version' => '8.0.12',
    ],
    '@orchidjs/sifter' => [
        'version' => '1.1.0',
    ],
    '@orchidjs/unicode-variants' => [
        'version' => '1.1.2',
    ],
    'tom-select/dist/css/tom-select.default.min.css' => [
        'version' => '2.4.1',
        'type' => 'css',
    ],
    'tarteaucitronjs' => [
        'version' => '1.19.0',
    ],
    'side-channel-list' => [
        'version' => '1.0.0',
    ],
    'side-channel-map' => [
        'version' => '1.0.1',
    ],
    'side-channel-weakmap' => [
        'version' => '1.0.2',
    ],
    'es-object-atoms' => [
        'version' => '1.0.0',
    ],
    'math-intrinsics/abs' => [
        'version' => '1.1.0',
    ],
    'math-intrinsics/floor' => [
        'version' => '1.1.0',
    ],
    'math-intrinsics/max' => [
        'version' => '1.1.0',
    ],
    'math-intrinsics/min' => [
        'version' => '1.1.0',
    ],
    'math-intrinsics/pow' => [
        'version' => '1.1.0',
    ],
    'math-intrinsics/round' => [
        'version' => '1.1.0',
    ],
    'math-intrinsics/sign' => [
        'version' => '1.1.0',
    ],
    'get-proto' => [
        'version' => '1.0.1',
    ],
    'get-proto/Object.getPrototypeOf' => [
        'version' => '1.0.1',
    ],
    'get-proto/Reflect.getPrototypeOf' => [
        'version' => '1.0.1',
    ],
    'call-bind-apply-helpers/functionApply' => [
        'version' => '1.0.1',
    ],
    'call-bind-apply-helpers/functionCall' => [
        'version' => '1.0.1',
    ],
    'call-bind-apply-helpers' => [
        'version' => '1.0.1',
    ],
    'call-bind-apply-helpers/applyBind' => [
        'version' => '1.0.1',
    ],
    'call-bound' => [
        'version' => '1.0.2',
    ],
    'dunder-proto/get' => [
        'version' => '1.0.1',
    ],
    'call-bind' => [
        'version' => '1.0.8',
    ],
];
