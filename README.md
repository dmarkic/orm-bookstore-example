# Bookstore

Example project using [Framework-X](https://github.com/clue/framework-x/), [blrf/dbal](https://github.com/dmarkic/dbal) and [blrf/orm](https://github.com/dmarkic/orm).
A simple Restful Api to showcase current `blrf/dbal` and `blrf/orm` development.

## Install

Pull the respository from github.

Run:

```
composer install
```

## Configuration

Create database and user that can access it.

Create `.env` file with following content. Replace relevant values.

```
DB_HOST=localhost
DB_PORT=3306
DB_USER=user
DB_PASSWD=pass
DB_DBNAME=bookstore
```

### Import database

Create database:

```
mysql -u user -p pass bookstore < db/bookstore_db.sql
```

Create data:

```
mysql -u user -p pass bookstore < db/bookstore_data.sql
```

## Test server

If you need to change port and/or bind address:

```
export X_LISTEN="tcp://127.0.0.1:8888"
```

Run server

```
php public/index.php
```

## Database and Models

Database is available in `db/` directory.

All models are defined in `src/Model` directory.

Models are available via restful Api: http://localhost:8080/bookstore/`modelName`.

### `Address` model

This model is configured using Attributes. It has one relation:

- [Country](#country-model)

### `AddressStatus` model

This model is configured using Attributes. It has no relations on it's own, but is related in [CustomerAddress](#customeraddress-model).

### `Book` model

This model meta-data is configured using `Model::ormMetaData()`. It's an example on how to describe models meta-data without Attributes.

This model has 2 `ONETOONE` relations:

- [BookLanguage](#booklanguage-model)
- [Publisher](#publisher-model)

### `BookLanguage` model

This model meta-data is configured using Attributes. It has no relations on it's own, but is related in [Book](#book-model).

### `Country` model

This model meta-data is configured using Attributes. It has no relations, but is related in [Address](#address-model).

### `Customer` model

This model meta-data is configured using Attributes. This model showcases `ONETOMANY` relation, where one customer may have many [CustomerAddress](#address-model)s.

As an example in this Api, there's a special endpoint which enables you to find addresses belonging to certain customer.

Api: http://localhost:8080/bookstore/customer/{customerId}/address

You can search addresses by `POST`ing query to this address. Example:

```
curl -X POST http://localhost:8080/bookstore/customer/3/address 
```

You can also provide additional query parameters, for example:

```
curl -X POST http://localhost:8080/bookstore/customer/3/address -d '{"where": ["status_id", "=", "1"], "limit": 1}'
```

Or with parameters:

```
curl -X POST http://localhost:8080/bookstore/customer/3/address -d '{"where": ["status_id"], "parameters": [1], "limit": 1}'
```

### `CustomerAddress` model

This model meta-data is configured using Attributes. It's an example where `composite` primary index is used (it has two primary columns: customer_id, address_id).
It has three `ONETOONE` relations:

- [Customer](#customer-model)
- [Address](#address-model)
- [AddressStatus](#addressstatus-model)

### `Publisher` model

This model meta-data is configured using Attributes.

This example showcases `magic` methods for getters and setters:

- `getPublisherId(): PromiseInterface;`
- `setPublisherName(): PromiseInterface;`
- `getPublisherName(): PromiseInterface;`

It also creates a `ONETOMANY` relation to [Book](#book-model)s. So you may use `$publisher->getBooks()` to find books released by this publisher.

## Api

### Get model

```
curl http://localhost:8080/bookstore/book/1
```

Output:

```json
{
    "book_id": 1,
    "title": "The World's First Love: Mary  Mother of God",
    "isbn": "8987059752",
    "num_pages": 276,
    "publication_date": "1996-09-01",
    "language": 2,
    "publisher": 1010
}
```

Get model with related fields resolved:

```
curl http://localhost:8080/bookstore/book/1/related
```

Output

```json
{
    "book_id": 1,
    "title": "The World's First Love: Mary  Mother of God",
    "isbn": "8987059752",
    "num_pages": 276,
    "publication_date": "1996-09-01",
    "language": {
        "language_id": 2,
        "language_code": "en-US",
        "language_name": "United States English"
    },
    "publisher": {
        "publisher_id": 1010,
        "publisher_name": "Ignatius Press"
    }
}
```

### Create model

```
curl -X PUT http://localhost:8080/bookstore/book -H 'Content-Type: application/json' -d '{"title":"From curl", "isbn": "123", "language": 2}'
```

### Delete model

```
curl -X DELETE http://localhost:8080/bookstore/book/11279
```

### Find models


```
curl -X POST http://localhost:8080/bookstore/book -d '{"limit": 10}'
```

Request streaming response which will return newline-delimited JSON.

```
curl -X POST http://localhost:8080/bookstore/book/stream -d '{"limit": 1000}'
```

Find all books which title starts with leter 'a':

```
curl -X POST http://localhost:8080/bookstore/book/stream -d '{"where": ["title", "LIKE"], "parameters": ["a%"]}'
```

### Get meta

You can obtain model meta-data by calling

```
curl http://localhost:8080/bookstore/book/metadata
```
