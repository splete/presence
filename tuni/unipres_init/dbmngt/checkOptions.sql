select count(*),etudRef from options o1 where
not exists (select * from options o2 where o2.optGroupeRef like concat(o1.optGroupeRef,'1'))
 group by etudRef order by count(*)