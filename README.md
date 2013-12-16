Elgar's Coding Orchestra
=====================

Our totally unnamed project is still pretty sparse. We currently have a server set up at spe.sneeza.me, and all members of our group have an account on that server with sudo access. Your default password will be 'password' which you can change by running the passwd command.

Project Page
---------------------

We currently have a public facing website located at:

```
http://project.spe.sneeza.me/
```

Our API
---------------------

If you wish to access the API, it is currently hosted at:

```
http://api.spe.sneeza.me/
```

It requires no authentication and allows simple method names to be appended to the URL in order to access different functionality.

### GET /select

This method returns a subset of data from the specified collection, with a few optional parameters for helping fine-tune things. These are as follows:

+ **dataset** - This parameter specifies the dataset that we should be running the query against.
+ **query** - Query to perform (takes MongoDB syntax).
+ **rows** _(optional)_ - The maximum number of rows to return. If left blank, it will return all of them.
+ **offset** _(optiona)_ - The offset starting point of the returned data. Mostly used in conjunction with 'rows'.
+ **fields** _(optional)_ - Specify the field names to return for each row. Should be a JSON encoded array of field names, eg. ['field1', 'field2'].

Here is an example request:

```php
$url = "http://api.spe.sneeza.me/select";

$args = array(
    "dataset" => "test",
    "query" => json_encode(
        array(
            "postcode" => "BS1"
        )
    ),
    "num_rows" => 50,
    "offset" => 0,
    "fields" => json_encode(
        array(
            "first_name",
            "last_name"
        )
    )
);

$args = http_build_query($args);
$data = json_decode(file_get_contents($url . "?" . $args), true);

var_dump($data);
```

### POST /insert

This method takes two inputs, specifying the dataset to insert documents into and the document(s) themselves. We use batch processing to speed up the time it takes to insert large amounts of data.

+ **dataset** - This parameter specifies the dataset that we should be inserting the documents into.
+ **document** _(optional)_ - Specifies one document, JSON encoded, to insert into the dataset.
+ **documents** _(optional)_ - Specifies an array of documents, JSON encoded, to insert into the dataset.

Here is an example insertion request for a single document:

```php
$url = "http://api.spe.sneeza.me/insert";

$args = array(
    "dataset" => "test",
    "document" => json_encode(
        array(
            "postcode" => "BS1",
            "first_name" => "Peter",
            "last_name" => "Parker"
        )
    )
);

$options = array(
    "http" => array(
        "header" => "content-type: application/x-www-form-urlencoded\r\n",
        "method" => "POST",
        "content" => http_build_query($args),
    )
);

$context  = stream_context_create($options);
$data = json_decode(file_get_contents($url, false, $context), true);

var_dump($data);
```

### POST /update

This method allows the user to update records in the database. You can specify a query which will select multiple records to update, much like the /select request. Any number of changes can be specified, with new fields being defined as well as old fields being unset (using the magical $unset keyword).

+ **dataset** - This parameter specifies the dataset that we should be updating the documents in.
+ **query** - Specify the query to select which records to update.
+ **changes** - Specifies the changes, JSON encoded, to update in the matched records.

Here is an example request to update any record having the last name 'Parker'.

```php
$url = "http://api.spe.sneeza.me/update";

$args = array(
    "dataset" => "test",
    "query" => json_encode(
        array(
            "last_name" => "Parker"
        )
    ),
    "changes" => json_encode(
        array(
            "\$set" => array(
                "postcode" => "BS2",
                "last_name" => "Piper",
            ),
            "\$unset" => array(
                "middle_name"
            )
        )
    )
);

$options = array(
    "http" => array(
        "header" => "content-type: application/x-www-form-urlencoded\r\n",
        "method" => "POST",
        "content" => http_build_query($args),
    )
);

$context  = stream_context_create($options);
$data = json_decode(file_get_contents($url, false, $context), true);

var_dump($data);
```

### POST /delete

This method allows the user to delete records from the database. You can specify a query which will select multiple records to delete, much like the /select request. This endpoint doesn't allow you to specify a null query.

+ **dataset** - This parameter specifies the dataset that we should be deleting the documents from.
+ **query** - Specify the query to select which records to delete.

Here is an example request to delete all records with the last name 'Piper'.

```php
$url = "http://api.spe.sneeza.me/delete";

$args = array(
    "dataset" => "test",
    "query" => json_encode(
        array(
            "last_name" => "Piper"
        )
    )
);

$options = array(
    "http" => array(
        "header" => "content-type: application/x-www-form-urlencoded\r\n",
        "method" => "POST",
        "content" => http_build_query($args),
    )
);

$context  = stream_context_create($options);
$data = json_decode(file_get_contents($url, false, $context), true);

var_dump($data);
```

### GET /calc/polyfit

This method allows the caller to determine the equation of an n-th degree polynomial representing the correlation between two sets of data. The inputs are as follows:

+ **dataset** - This parameter specifies the dataset that we should be running the query against.
+ **field_one** - The first field inside the dataset to query.
+ **field_two** - The second field inside the dataset to compare the first to.
+ **degree** _(optional)_ - Specify the degree of the required polynomial. Defaults to **2**.

Here is an example request:

```php
$url = "http://api.spe.sneeza.me/calc/polyfit";

$args = array(
    "dataset" => "test",
    "field_one" => "age",
    "field_two" => "wealth"
    "degree" => 2
);

$args = http_build_query($args);
$data = json_decode(file_get_contents($url . "?" . $args), true);

var_dump($data);
```

The fields returned in the 'data' section will be an array of coefficients, ordered by the highest degree to the lowest degree. These can then be used to graph the equation or evaluate the expression at different points.