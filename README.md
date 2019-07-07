# Alograg DevTools

[![Version](https://img.shields.io/badge/version-v0.1.5-blue.svg)](https://github.com/alograg/dev-tools) [![license](https://img.shields.io/github/license/alograg/dev-tools.svg)](https://github.com/alograg/dev-tools/blob/master/LICENSE) [![Version](http://img.shields.io/packagist/v/alograg/dev-tools.svg)](https://packagist.org/packages/alograg/dev-tools) [![Downloads](http://img.shields.io/packagist/dm/alograg/dev-tools.svg)](https://packagist.org/packages/alograg/dev-tools)

Dev tools for Lumen/Laravel projects

Based on:
- [Atnic/lumen-generator](https://github.com/Atnic/lumen-generator)
- [webNeat/lumen-generators](https://github.com/webNeat/lumen-generators)
- [flipbox/lumen-generator](https://github.com/flipboxstudio/lumen-generator)

## Instalation

```SH
composer require --desv alograg/dev-tools 
```

## Artisan Tools

- [key:generate](#key:generate)

### key:generate

```
Description:
  Set the application key

Usage:
  key:generate [options]

Options:
      --key[=KEY]       Key to modify files [default: "APP_KEY"]
  -s, --show            Display the key instead of modifying files
```
