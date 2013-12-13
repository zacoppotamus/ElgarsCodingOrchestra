Elgar's Coding Orchestra
=====================

Our totally unnamed project is still pretty sparse. We currently have a server set up at spe.sneeza.me, and all members of our group have an account on that server with sudo access. Your default password will be 'password' which you can change by running the passwd command.

Our API
---------------------

If you wish to access the API, it is currently hosted at:

http://api.spe.sneeza.me/

It requires no authentication and allows simple method names to be appended to the URL in order to access different functionality. For example, if you wanted to return some rows of data from the API, you would use the following:

```
$data = file_get_contents("http://api.spe.sneeza.me/query?d=testdata&offset=0&rows=100");
```