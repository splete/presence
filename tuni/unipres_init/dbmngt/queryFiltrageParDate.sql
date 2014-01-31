SELECT ADDDATE(d1.date,INTERVAL d2.nbseance WEEK), d1.nomMatiere, d1.prof, d1.groupeRef, d1.salle,d1.debut,d1.fin FROM 
      (select ADDDATE(
          d3.premierJourMois,
          INTERVAL (-DAYOFWEEK(d3.premierJourMois)+jour) DAY) as date,
          matiere.nom as nomMatiere,
          profRef as prof,
          salleRef as salle,
          seance.debut as debut,
          ADDTIME(seance.debut,intervention.duree) as fin,
          date1ereseance,dateDerniereSeance,
          groupeRef
      from seance inner join intervention on interventionRef=interventionCle
            inner join matiere on matiereCle=matiereRef,
           

          (select 
            DATE(CONCAT(YEAR(CURRENT_DATE()),'-',MONTH(CURRENT_DATE())+1,'-01'))
                as premierJourMois ) d3
        ) d1,
        (select 0 as nbseance union select 1 as nbseance union select 2 as nbseance union select 3 as nbseance union select 4 as nbseance) d2
where (MONTH(ADDDATE(d1.date,INTERVAL d2.nbseance WEEK))=MONTH(CURRENT_DATE()) +1)
and ADDDATE(d1.date,INTERVAL d2.nbseance WEEK) between d1.date1ereseance and d1.dateDerniereSeance    
    
    
