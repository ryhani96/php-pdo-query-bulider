# PHP QUERY BULIDER 

a simple class allows you to do database opiations  in fast and secur way 



## Usage
`conn.php` 
```php
//include the Databse.class.php file and QueryBulider.class.php
$db= new Database("yourhost","dbName","user","pass");
```
`index.php`
```php
require_once("conn.php");
$q= new QueryBuilder($db);
//inserting 
$q->table("yourtable")->insert([
   "databsecolumn1"=>$value1,
   "databsecolumn2"=>$value2,
   //...
])->excute();

//update 

$q->table("yourtable")->update([
    "databsecolumn1"=>$value1,
    "databsecolumn2"=>$value2,
   //...
])
->where("col",QueryBuilder::EQUALS,"val"/*, logical opirator default AND works on multiple whre usage*/)
->execute();

//listing 
$q->table("yourtable")
    ->select("*")
    ->where("col", QueryBuilder::EQUALS, "value", QueryBuilder::OR)
    ->where("col", QueryBuilder::NOT_EQUAL, null)
     //...
    ->OrderBy("col2", QueryBuilder::DESC)
    ->limit(10)
    ->get();
```
where method takes 3 main parameters and 1 optinal  

 **where**(`col`,`opirator`,`value`,optinal: `logical_oprirations`);
####  `col` the database colmumn name
####  `opirator` like  "< > != =" and it can be used easly by using the class constans `EQUALS`  `GREATER`  `SMALLER`  `NOT_EQUAL`
#### `value` the condation value
#### `logical_oprirations` database logical oprirations (and ,or) 
 
## Contributing

Pull requests are welcome. For major changes, please open an issue first
to discuss what you would like to change.



## contact
find me at [instagram](https://instagram.com/ryhani96) 
## License

[MIT](https://choosealicense.com/licenses/mit/)