-- запросы по пунктам задания без использования php для расчетов
-- 1) определить максимальный возраст
select max(age) from person;

-- 2) найти любую персону, у которой mother_id не задан и 
-- возраст меньше максимального
select * from person 
where mother_id is null 
and age < (select max(age) from person)  order by rand() limit 1;

-- 3) изменить у нее возраст на максимальный

update person set age=
(select * from (select max(age) from person) as p1)
where id=(select * from (select id from person 
where mother_id is null 
and age < (select max(age) from person)  
order by rand() limit 1) as p1);

-- 4) получить список персон максимального возраста (фамилия, имя). 
-- желательно не используя полученное на шаге 1 значение.

select lastname, firstname from person
where age = (select max(age) from person);