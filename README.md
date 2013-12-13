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

### Method: /query

This method returns a subset of data from the specified collection, with a few optional parameters for helping fine-tune things. These are as follows:

+ **d** - This parameter specifies the dataset that we should be running the query against.
+ **rows** _(optional)_ - The maximum number of rows to return. If left blank, it will return all of them.
+ **offset** _(optiona)_ - The offset starting point of the returned data. Mostly used in conjunction with 'rows'.
+ **fields** _(optional)_ - Specify the field names to return for each row. Should be a JSON encoded array of field names, eg. ['field1', 'field2'].

Here is an example request:

```php
$dataset = "test";
$num_rows = 50;
$fields = json_encode(array('first_name', 'last_name'));

$url = "http://api.spe.sneeza.me/query?d=" . urlencode($dataset) . "&rows=" . $num_rows . "&fields=" . urlencode($fields);
$data = json_decode(file_get_contents($url), true);

var_dump($data);
```
