
loadFunctions()
//
/* vide la zone de texte du filtre ldap*/
function clearField()
{

	document.getElementById('txt_filter').value='';
}
/* case à cocher de sélection de toutes les lignes*/


/*-------------------------------------------------------------------------------------------------------
//cette fonction de gestion du du contr?le select  
//-------------------------------------------------------------------------------------------------------*/

function selectAll() 
{
    //loadFunctions();
    //
    var count=document.getElementById('Ldap2Mysql_form').elements.length;
    var sPrefixe='TR_';
    var iElement;
    //
    for (iElement =0; iElement < count; iElement++)
    {
        //si l'?l?ment existe
        if(document.getElementById('checkbox_'+iElement))
        {
            if (document.getElementById('checkbox_selectAll').checked==true)
            {
                
                    document.getElementById('checkbox_'+iElement).checked = true ;
                    document.getElementById('TR_'+iElement).bgColor = '#f7f3ff' ;
                
            }
            else
            {
                    document.getElementById('checkbox_'+iElement).checked = false ; 
                    //on remet les param?tres de d?part
                    loadFunctions();
            }
        }
        
    }
    
    
}//fin fonction selectAll


//

//
function loadFunctions()
{

        /*-------------------------------------------------------
        //gestion des ?v?nements sur les lignes (balise TR)
        -------------------------------------------------------*/
        TR = document.getElementsByTagName('tr');
        
        var prefixeTR='TR_';
        var prefixeCheckbox='checkbox_';
        var i;
        var iCouleur=0;
        
        
        for(i in TR)
        {
        //

            if ( TR[i].id) 
            {
                //gestion de l'alternance des couleurs
                
                var iCouleur=TR[i].id.replace('TR_','');
    
                
                if (iCouleur%2) 
                {                                   
                    TR[i].bgColor = '#fcfcfc' ;                 
                }
                else 
                {                           
                    TR[i].bgColor ='#ffffff' ;
                }
              
               
            
                
                
                //gestion du clic sur la ligne
                TR[i].onclick =TrOnClick;
                
                //
                //transformation du curseur texte en curseur fl?che
                //et focus sur la ligne
                TR[i].onmouseover =                 
                TrOnMouse;//appel de la fonction TrOnMouse (ATTENTION  sans les accolades)
                TR[i].style.cursor= 'default';
                //
                //? la perte de focus
                //TR[i].onmouseout =TrOnMouse;
                //click sur le checkbox
                var iCheckboxId=TR[i].id.replace('TR_','checkbox_');
                document.getElementById(iCheckboxId).onclick=TrOnClick;
                document.getElementById(iCheckboxId).style.cursor= 'pointer'; 
                //
                
            }//fin if
        //
        //mettre un curseur sur le checkbox_selectAll
        var check_all= document.getElementById('checkbox_selectAll');
        if(check_all)
        {
             document.getElementById('checkbox_selectAll').style.cursor= 'pointer';
        }
           
       
        /*-------------------------------------------------------
        //gestion des ?v?nements sur les checkbox 
        -------------------------------------------------------*/
        //
        /*-------------------------------------------------------
         fonction d'activation du menu
        -------------------------------------------------------*/
        
        //          
    }//fin for
}//fin fonction loadFunctions
//
function TrOnMouse()
{   
    var nombreTD = document.getElementById(this.id).childNodes.length;

    var index;  
    for (index = 0; index < nombreTD; index++) 
    {
        //var ligne=document.getElementById(this.id).childNodes[index];
        //ligne.style.borderTop = '1px solid red';
        //document.getElementById(this.id).childNodes[index].style.borderBottom = '1px solid red';          

    }
}  
//
function TrOnClick()
{
    //on r?cup?re l'id du checkbox qui porte le m?me num?ro que la balise tr sans les pr?fixes
    //exemple 'TR_22' pour r?cup?rer checkbox_22;
    //
    var prefixeTR='TR_';
    var prefixeCheckbox='checkbox_';
    var TR_id=this.id;
    var checkbox_id;
    checkbox_id=TR_id.replace(prefixeTR,prefixeCheckbox);
    //
    if(document.getElementById(checkbox_id).checked==true)
    {       
        document.getElementById(checkbox_id).checked=false;//le code couleur doit ?tre toujours en minuscule
        document.getElementById(TR_id).bgColor = '#ffffff' ;
        //document.getElementById(TR_id).borderColor='red';
        //document.getElementById(TR_id).borderColor='red'; 
    }
    else
    {  
        document.getElementById(checkbox_id).checked=true;
        document.getElementById(TR_id).bgColor = '#f7f3ff' 
    }
    //  
    //todo si tous les checkbox sont coches alors on coche le checkbox selectAll
    

}; 


    
   
