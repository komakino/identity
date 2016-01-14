# Identity

Identity is a composer package to validate, parse, format and extract various information from national identity numbers.

**Current implementations**:
* Swedish personnummer/organisationsnummer
* Danish personnummer/CPR-nummer
* Finnish henkilötunnus/personbeteckning

## Installation

To add this package as a dependency to your project, simply add a dependency on `komakino/identity` to your project's `composer.json` file.
```json
    {
        "require": {
            "komakino/identity": "*"
        }
    }
```
## Usage

```php
use Komakino\Identity\National\SwedishIdentity;
use Komakino\Identity\National\DanishIdentity;

$swedishIdentity = new SwedishIdentity('011017-2721');
$danishIdentity = new DanishIdentity('170583-7315');
```

### Common methods

Upon construction, the number is parsed and validated.

#### Public properties

* bool $valid
    * The validity of the identity number

* mixed \*\*getters\*\*
    * All number properties are accessible as properties on the instance

#### Public methods

* array **listProperties**()
    * Returns all number properties

* bool **hasProperty**(string $property)
    * Checks if the identity's implementation has a property

* string **__toString**()
    * Output the formatted identity number

#### Static methods

* static array **parse**(string $number)
    * Creates an instance and returns all number properties

* static bool **validate**(string $number)
    * Creates an instance and returns the validity of the number

* static string **format**(string $number)
    * Output the formatted identity number

### Swedish personnummer/organisationsnummer

#### Allowed input
* `0110172721`
* `011017-2721`
* `011017+2721`
* `20011017-2721`

#### Formatted output
`011017-2721`

#### Number properties
* **type**
    * *organization*/*person*
* **century**
    * Sources for century:
        * Provided in number as **OO**xxxxxx-xxxx
        * The separator is a *+*, which denotes a person is over 100
        * By logical guessing. Pseudo: `year > current_year ? 19 : 20`
* **year**
    * **OO**xxxx-xxxx
* **month**
    * xx**OO**xx-xxxx
* **day**
    * xxxx**OO**-xxxx
* **centuryHint**
    * xxxxx**-**xxxx
    * Defaults to *-*
* **locality**
    * xxxxxx-**OO**xx
* **county**
    * Only available for people born before 1990
* **number**
    * xxxxxx-xx**O**x
* **gender**
    * *male*/*female*
* **checkdigit**
    * xxxxxx-xxx**O**
* **birthday**
    * A **DateTime** object
* **temporary**
    * If the number is of a temporary nature

### Danish personnummer/CPR-nummer

#### Allowed input
* `1705837315`
* `170583-7315`

#### Formatted output
`170583-7315`

#### Number properties
* **century**
    * Calculated from *year* and *centuryHint*
* **day**
    * **OO**xxxx-xxxx
* **month**
    * xx**OO**xx-xxxx
* **year**
    * xxxx**OO**-xxxx
* **centuryHint**
    * xxxxxx-**O**xxx
* **sequence**
    * xxxxxx-**OOO0**
* **gender**
    * *male*/*female*
* **birthday**
    * A **DateTime** object


### Finnish personnummer/CPR-nummer

#### Allowed input
* `311280-888Y`

#### Formatted output
`311280-888Y`

#### Number properties
* **century**
    * Defined by *centuryHint*
* **day**
    * **OO**xxxx-xxxx
* **month**
    * xx**OO**xx-xxxx
* **year**
    * xxxx**OO**-xxxx
* **centuryHint**
    * xxxxx**-**xxxx
    * *-*/*+*/*A*
* **number**
    * xxxxxx-**OOO**x
* **checkdigit**
    * xxxxxx-xxx**O**
* **gender**
    * *male*/*female*
* **birthday**
    * A **DateTime** object