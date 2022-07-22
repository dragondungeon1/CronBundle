# CronBundle
A cron job manager for symfony console.

## Installation

A simple bundle install. No extra stuff.

```composer require padam87/cron-bundle```

```new Padam87\CronBundle\Padam87CronBundle(),```

## Configuration

```yaml
padam87_cron:
    log_dir: %cron_log_dir%
    variables:
        mailto: %cron_mailto%
        any_other_variable_you_might_need: 'some_value'
```

## Usage

### Commands

- `cron:dump` dumps the cronfile
- `cron:import` dumps the cronfile to the temp dir, and imports it

### Basic

#### php 7.4
```php
/**
 * @Cron\Job(minute="5", hour="0")
 */
class MyCommand extends Command
```
#### php 8
```php
#[Job(minute: '5', hour: '0')]
class MyCommand extends Command
```

### Groups
#### php 7.4
```php
/**
 * @Cron\Job(minute="5", hour="0", group="master")
 */
class MyCommand extends Command
```
#### php 8
```php
#[Job(minute: '5',hour: '0')]
class MyCommand extends Command
```

### Output file
#### php 7.4
```php
/**
 * @Cron\Job(minute="5", hour="0", logFile="my-command.log")
 */
class MyCommand extends Command
```

#### php 8
```php
#[Job(minute: '5', hour: '0',  logFile: 'my-command.log')]
class MyCommand extends Command
```


