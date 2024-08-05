Реализовано на Laravel. API для расчета зарплаты сотрудников, работающих на почасовой основе. Сотрудники могут отправлять количество отработанных часов, а система будет хранить эту информацию и предоставлять возможность выплаты зарплаты по запросу.

**Технологии**
- PHP 8.1
- Laravel
- MySQL
- Docker
- Composer

**Установка**

	git clone https://github.com/Dmitr1yS/Task1-LaravelApi.git
	
	cd Task1-LaravelApi
	
	docker-compose up -d
	
	docker-compose exec app bash
	
	php artisan migrate


**Тестирование**
docker-compose exec app php artisan test

**Использование API**
    API имеет следующие эндпоинты:
    
    **Создание сотрудника**
    POST /api/employees
    Тело запроса:
    
    {
        "email": "test@example.com",
        "password": "password123"
    }
    
    Ответ: JSON объект с информацией о созданном сотруднике.
    
    **Прием транзакции (количество отработанных часов)**
    POST /api/hours
    Тело запроса:
    
    {
        "employee_id": 1,
        "hours": 8
    }
    
    Ответ: JSON объект с информацией о сохраненной записи.
    
    **Вывод невыплаченных сумм зарплат**
    GET /api/salaries
    Ответ: JSON массив с невыплаченными суммами зарплат:
    
        {
            "employee_id": 1,
            "total": 800
        },
        ...
        
    **Выплата всех накопившихся сумм**
    
    POST /api/pay-salaries
    Ответ: JSON сообщение о том, что все зарплаты выплачены.
