function validate1()
		{
			
			if(document.form.email.value=='������� ���� Email �����')
			{
				alert('Please Provide Your Email Address');
				document.form.email.value='';
				document.form.email.focus();
				return false;
			
			}
			
			if(document.form.email.value.length<1)
			{
				alert('���������� ������� ���� Email �����');
				document.form.email.focus();
				return false;
			
			}
			
			else return true;
		
		
		}



		function validate()
		{
			
			if(document.form1.txtName.value.length<1)
			{
				alert('���������� ������� ���� ���');
				document.form1.txtName.focus();
				return false;
			
			}

			

			else if(document.form1.txtEmail.value.length<1)
			{
				alert('���������� ������� ���� Email �����');
				document.form1.txtEmail.focus();
				return false;
			
			}
			
			else if(document.form1.txtComment.value.length<1)
			{
				alert('���������� ������� ���������');
				document.form1.txtComment.focus();
				return false;
			
			}

			
			else return true;
		
		
		}
		
	function val(field,n)
	{

	switch(n)
	{
		
			
	case 5:
				var valid = "0123456789"
				var ok = "yes";
				var temp;
				for (var i=0; i<field.value.length; i++)
			 	{
					temp = "" + field.value.substring(i, i+1);
					if (valid.indexOf(temp) == "-1") ok = "no";
				}
					
					if (ok == "no") 
					{
						alert("������ ! ���������� ������� �������� ��������.");
						field.focus();
						field.select();
		   			}
			break

	case 6:
			
				var valid = "abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ."
				var ok = "yes";
				var temp;
				for (var i=0; i<field.value.length; i++)
			 	{
					temp = "" + field.value.substring(i, i+1);
					if (valid.indexOf(temp) == "-1") ok = "no";
				}
				
				if (ok == "no") 
				{
					alert("������ ! ���������� ������� ���������� ������.");
					field.focus();
					field.select();
		   		}
			break
			
		
	

	 case 11: 
	 			var valid = "0123456789/, +()-"
				var ok = "yes";
				var temp;
				for (var i=0; i<field.value.length; i++)
			 	{
					temp = "" + field.value.substring(i, i+1);
					if (valid.indexOf(temp) == "-1") ok = "no";
				}
					
					if (ok == "no") 
					{
						alert("������ ! ���������� ������� ���������� ������.");
						field.focus();
						field.select();
		   			}
			break
	
	case 9:
			 var valid = "`~&'^"
			var ok = "yes";
			var temp;
			var temp1;
			for (var i=0; i<field.value.length; i++)
			 {
				temp = "" + field.value.substring(i, i+1);
				if (valid.indexOf(temp) >= 0) 
				{
				ok = "no";
				temp1=temp;
				}
				
			}
				
				missinginfo = "";
			if(field.value!="")	
				{
					if ((field.value.indexOf('@') == -1) || 
					(field.value.indexOf('.') == -1)) 
						{
						missinginfo += "\n     -  Email �����";
						}
				}		
					if (missinginfo != "")
			 			{
							missinginfo ="_____________________________\n" +
							"�� �������� � �����:\n" +
							missinginfo + "\n_____________________________" +
							"\n���������� ��������� ������ � ��������� �����!";
							alert(missinginfo);

							field.focus();
							field.select();
							
						} 
			 	 			else	

								if (ok == "no") 
								{
	
								alert("( " + temp1 + " )" + " �� ��������� ! ���������� ������� ���������� ������.");
								field.focus();
								field.select();
								
		   						}
		   					

			break


	 case 12:
			
				var valid = "abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ()."
				var ok = "yes";
				var temp;
				for (var i=0; i<field.value.length; i++)
			 	{
					temp = "" + field.value.substring(i, i+1);
					if (valid.indexOf(temp) == "-1") ok = "no";
				}
				
				if (ok == "no") 
				{
					alert("������ ! ���������� ������� ���������� ������.");
					field.focus();
					field.select();
		   		}
				break
			
		


			
	}//switch
}//function
		
