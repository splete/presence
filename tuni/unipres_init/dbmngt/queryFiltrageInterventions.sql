select 
          matiere.matiereCle as matiereCle,
          intervention.interventionCle as interventionCle,
          profRef as prof,
          salleRef as salle,
          seance.debut as debut,
          ADDTIME(seance.debut,intervention.duree) as fin,
          date1ereseance,dateDerniereSeance,
          groupeRef
      from seance inner join intervention on interventionRef=interventionCle
            inner join matiere on matiereCle=matiereRef
           
where 
  ADDDATE(CURRENT_DATE(),INTERVAL 0 MONTH) between date1ereseance and dateDerniereSeance 
    
    
