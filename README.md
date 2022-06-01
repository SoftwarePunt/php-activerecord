# softwarepunt/php-activerecord

**This is a fork of php-activerecord, originally authored by [@kla](https://github.com/kla), [@jpfuentes2](https://github.com/jpfuentes2) and its [contributors](https://github.com/kla/php-activerecord/contributors).**    

## Important

**⚠ `php-activerecord` is no longer actively being maintained, the original site is dead, and the project is mostly abandoned.**

I would ___not___ recommend this ORM for any new projects or developments.

This fork contains some quality-of-life enhancements, support for the latest PHP version, and fixes, which are relevant to us and some of our older projects that we maintain.

## Installation

We recommend using composer:

    composer require softwarepunt/php-activerecord:dev-master

Because we do not actively maintain tags or versions for this project, you should refer to `dev-master`. 

## Documentation & usage

- 👨‍💻 *The original project:* https://github.com/jpfuentes2/php-activerecord
- 📕 *Mirror of the old docs site:* https://www.phpactiverecord.xyz/

(⚠ phpactiverecord.org is dead and being squatted by someone, best to ignore it.)

## Fork changes

**This fork is currently only compatible with PHP 8.1!**

### Fixes

- Compatibility fixes over time for PHP 7.2, 8.0, 8.1
- `activerecord_autoload` no longer causes conflicts when the library is loaded more than once.
- Fix `count(): Parameter must be an array or an object that implements Countable` error in `find_by_pk()`.
- Fix `castIntegerSafely()` throwing an error if the input is empty (e.g. empty string), now just returns zero.
 
### Enhancements

- DateTime: Implement \JsonSerializable, format in "c" mode.
- Added `set_is_new_record(bool)` to `Model` to help modifying internal state.
- Added experimental `reconnect()` function to `Connection`.
- Added `stupidfastquery($options)`: this converts options to a query and returns the raw PDO result set without any activerecord magic.
- Added `stupidfastqueryValues($options, $colIndex = 0)`: runs a `stupidfastquery` and returns an array of column values.
- Added `stupidfastcount($options)`: run query, only return row count. 
- Added `find_all_with_wrapped_sort($options)`: this wraps the results in a separate SELECT for faster sorting on JOIN heavy queries.
- Pass `query_info` to `find($options)` options to get generated query data as result (used internally by `stupidfastcount` & co) - can be used if you want to convert `$options` to an actual statement: 

    ```php
    [
      'query' => $sql->to_s(),
      'values' => $sql->get_where_values(),
      'readonly' => $readonly,
      'eager_load' => $eager_load
    ];
    ``` 
