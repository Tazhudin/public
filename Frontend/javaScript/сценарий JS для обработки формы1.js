var inputBut = document.getElementById("inputDate");
							inputBut.onclick=function()
									{
										var fname = document.getElementById("fname1").value;
										var name = document.getElementById("name1").value;
										var oname = document.getElementById("oname1").value;



										var select1=document.getElementsByTagName("SELECT")
										var select1res=0;
										select1res = select1.selected;


										var radbutres=0;
										var rdbtn= document.getElementsByName("radiobutton1");
										 for (var i = 0; i < rdbtn.length; i++) 
											 {
											 	if (rdbtn[i].checked)
											 		radbutres=rdbtn[i].value;

											 }

										var chkbx1= document.getElementsByName("choose1");
										var chkbx1res="";
											for (var i = 0; i < chkbx1.length; i++) {
												if(chkbx1[i].checked)
													chkbx1res=chkbx1res+chkbx1[i].value;
											}

										var selec=document.getElementById("textaria");
										var selecRes="";
											for (var i = 0; i < Option.length; i++) {
												if(Option[i].selected)
													selecRes=Option[i];

											}



										alert("ФАМИЛИЯ: "+fname+"   ИМЯ: "+name+"   ОТЧЕСТВО: "+oname+"   ВАРИАНТ:" +select1res+"      ЛЮБИМЫЙ ЦВЕТ: "+radbutres +"        ЧЕКБОКС: "+chkbx1res + "    КОММЕНТАРИИ"+selecRes);

									}

var butDisp = document.getElementById("butDisp");
		butDisp.onclick=function()
					{
						


					}




