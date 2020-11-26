**Первое задание:**

`SELECT users.id as id, concat(users.first_name,' ',users.last_name) as name, MIN(books.author) as author,GROUP_CONCAT(books.name SEPARATOR ',') as books, 
COUNT(DISTINCT(books.author)) as count_author, COUNT(user_books.book_id) as count FROM users
	LEFT JOIN user_books on users.id = user_books.user_id 
	LEFT JOIN books on user_books.book_id = books.id 
	where "age" BETWEEN 7 and 17 
	GROUP BY users.id 
	HAVING COUNT(user_books.book_id)=2 and COUNT(DISTINCT(books.author)) = 1`

Вместо MIN(books.author) можно использовать DISTINCT(books.author), но требует включения "ONLY_FULL_GROUP_BY"

**Второе задание**

Основной контроллер **\App\Http\Controllers\Api\MainController**

Основные классы для конвертации и получения курсов валют **\App\Classes**

Основные конфиги по конвертации, комиссиям и приватным роутингам **configs**
Метод получения курса валют

**/api/v1?method=rates**

Params
**_currency_** - Выводи курс по конкретной валюте
