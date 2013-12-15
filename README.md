Elgar's Coding Orchestra
=====================

Our totally unnamed project is still pretty sparse. We currently have a server set up at spe.sneeza.me, and all members of our group have an account on that server with sudo access. Your default password will be 'password' which you can change by running the passwd command.

Our API
---------------------

If you wish to access the API, it is currently hosted at:

```
http://api.spe.sneeza.me/
```

It requires no authentication and allows simple method names to be appended to the URL in order to access different functionality.

### Method: GET /select

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
        array("postcode" => "BS1")
    ),
    "num_rows" => 50,
    "offset" => 0,
    "fields" => json_encode(
        array("first_name", "last_name")
    )
);

$args = http_build_query($args);
$data = json_decode(file_get_contents($url . "?" . $args), true);

var_dump($data);
```

### Method: POST /insert

This method takes two inputs, specifying the dataset to insert documents into and the document(s) themselves. We use batch processing to speed up the time it takes to insert large amounts of data.

+ **dataset** - This parameter specifies the dataset that we should be inserting the documents into.
+ **document** _(optional)_ - Specifies one document, JSON encoded, to insert into the dataset.
+ **documents** _(optional)_ - Specifies an array of documents, JSON encoded, to insert into the dataset.

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