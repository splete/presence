listeSG="car;car1;car2;car3;car4;car5;bda;bda1;bda2;bda3;ihm;ihm1;ihm2;ihm3;svl;svl1;svl2;svl3;calp;fdd;heci;m3ds;pac;ppd;rdf;ti;aea"
annee=2012
anneeC=12
echo select etudCle from etudiant inner join etudiant_groupe on etudRef=etudCle inner join stalt.contrat c on c.etudRef=etudCle and c.anneeCle=2012 where
annee = 2012 and groupeRef like \'M1\%FA\%\' and groupeRef not like \'\%MIAGE\%\' order by etudCle

echo $listeSG | tr ";" "\n" | while read sg 
do
echo delete from matiere_opt where annee=\"$annee\" and groupe=\"$sg\"
echo delete from options where annee=\"$annee\" and groupe=\"$sg\"  
echo processing $sg >&2
cours=`echo $sg | sed 's/[0-9]$//'`
code=xx`echo $cours`xx
grep ${code} m1s2_options.txt | cut -f 2 -d ';' | \
	awk -v groupe=$sg -v annee=$annee '{ print "insert into matiere_opt values (\"" groupe "\",\"" annee "\",\"" $0 "\");" }'
cat m1s2.csv | grep $sg | tr -d '"' | \
	awk -F ';' -v groupe=$sg -v annee=$annee '{split($1,np," "); print "insert into options values (\"" tolower(np[2]) "." tolower(np[1]) "\",\"" annee "\",\"" groupe "\");"}'
done
