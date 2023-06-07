	function Popup(Link,WinName,Height,Width,X,Y)
	{// Written By : Mohammad Kaiser Anwar
		Win = window.open(Link,WinName,"height="+Height+",width="+Width+",top="+Y+",left="+X+",location=no,menubar=0,statusbar=0,locationbar=0,scrollbars=1,resizable=0");
		return(Win);
	}
	
	function MenuPopup(Link)
	{
		var Win = Popup(Link,"KS_PrimeMedic_Add",740,1024,100,100);
		Win.focus();
	}

	function ShowImage(Picture,Code,ImageX,ImageY)
	{// Written By : Mohammad Kaiser Anwar
		ImageX = ImageX + 10;
		ImageY = ImageY + 20;
		Win = window.open('../../includes/imageviewer.php?Picture='+Picture+'&Code='+Code,'Kaiser','height='+ImageY+',width='+ImageX+',top=100,left=0,location=0,menubar=0,statusbar=0,locationbar=0,scrollbars=1,resizable=0');
		Win.focus();
	}

	function IsBlank(Text)
	{// Written By : Mohammad Kaiser Anwar
		Text = ReplaceChar(Text," ","");
		return(Text == "");
	}

	function IsEmpty(Text)
	{
		Text = ReplaceChar(Text," ","");
		return(Text == "");
	}

	function IsText(Text,ValidChars,Empty)
	{
		if (ValidChars == "Digits")
		{
			ValidChars = "0123456789";
		}
		else if (ValidChars == "Alpha")
		{
			ValidChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			Text = Text.toUpperCase();
		}
		else if (ValidChars == "AlphaNum")
		{
			ValidChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
			Text = Text.toUpperCase();
		}
		if (IsEmpty(Text) == true)
		{
			return(Empty);
		}
		for (var i = 0; i < Text.length; i++)
		{
			if (ValidChars.indexOf(Text.substring(i,i+1)) == -1)
				return (false);
		}
		return(true);
	}

	function IsBarcode(Barcode,Empty)
	{// Written By : Mohammad Kaiser Anwar
		if (IsEmpty(Barcode))
		{
			return(Empty);
		}
		return(IsText(Barcode,"Digits",Empty));
	}

	function IsNumber(Text,Empty,Decimal,Value)
	{// Written By : Mohammad Kaiser Anwar
		if (Text == "")
			return(Empty);
		else
		{
			if (isNaN(Text))
				return(false);
			else
			{
				if (Decimal == false)
				{
					if (Text.indexOf(".") != -1)
						return(false);
				}
				if (Value == 0 && parseFloat(Text) < 0)
					return(false);
				else if (Value == 1 && parseFloat(Text) <= 0)
					return(false);
				else
					return(true);
			}
		}
	}

	function IsPhone(Text,Empty)
	{// Written By : Mohammad Kaiser Anwar
		if (IsEmpty(Text) == true)
			return(Empty);
		else if (Text.length < 10)
			return(false);
		else if (IsText(Text,"+- 0123456789()") == false)
			return(false);
		else
		{
			for (var i = 0; i <= 9; i++)
			{
				Phone = ReplaceChar(Text,i,"");
				if (Phone.length == 0)
				{
					return(false);
				}
			}
		}
	}

	function IsMobile(Text,Empty)
	{
		if (IsEmpty(Text) == true)
		{
			return(Empty);
		}
		if (IsText(Text,"Digits") == false)
		{
			return(false);
		}
		if (Text.length != 10)
		{
			return(false);
		}
		if (Text.substring(0,2) != "04")
		{
			return(false);
		}
	}

	function IsMobilePhone(Text,Empty)
	{
		if (IsEmpty(Text) == true)
		{
			return(Empty);
		}
		if (IsText(Text,"Digits") == false)
		{
			return(false);
		}
		if (Text.length != 10)
		{
			return(false);
		}
		if (Text.substring(0,2) != "04" && Text.substring(0,2) != "02" 
			&& Text.substring(0,2) != "03" && Text.substring(0,2) != "07" && Text.substring(0,2) != "08")
		{
			return(false);
		}
	}

	function IsEmail(Text,Empty)
	{// Written By : Mohammad Kaiser Anwar
		if (IsEmpty(Text) == true)
		{
			return(Empty);
		}
		if (Text.indexOf('@') < 1)
		{// '@' cannot be in first position
			return(false);
		}
		if (Text.indexOf('@') != Text.lastIndexOf('@'))
		{// '@' only allowed once
			return(false);
		}
		if (Text.lastIndexOf('.') <= Text.indexOf('@') + 1)
		{// Must be atleast one valid char btwn '@' and '.'
			return(false);
		}
		if (Text.lastIndexOf('.') == Text.length - 1)
		{// Must be atleast one valid char after '.'
			return(false);
		}
		if (Text.indexOf('.') == 0)
		{// No Dot on first position permitted
			return(false);
		}
		if (Text.indexOf('.') + 1 == Text.indexOf('@'))
		{// No Dot on first position permitted
			return(false);
		}
		var ValidChar = " ;<>";
		for (var i = 0; i < Text.length; i++)
		{
			if (ValidChar.indexOf(Text.substring(i,i+1)) >= 0)
			{
				return(false);
			}
		}
		return(true);
	}

	function IsProviderNo(ProviderNo)
	{
		let locTable 	= "0123456789ABCDEFGHJKLMNPQRTUVWXY";
		let checkTable 	= "YXWTLKJHFBA";
		let weights 	= [3,5,8,4,2,1];
		var regEx = /^([0-9]{5,6})([{0123456789ABCDEFGHJKLMNPQRTUVWXY}])([{YXWTLKJHFBA}])$/;
		var matches = regEx.exec(ProviderNo);
		if (matches != null) 
		{
			// accommodate dropping of leading 0 
			var stem = matches[1];
			if (stem.length == 5)
			{
				stem = "0"+stem;
			}
			let location 	= matches[2];
			let checkDigit 	= matches[3][0];
			let plv = locTable.indexOf(location);
			let sum = plv * 6;
			weights.forEach( function(element, index) {
				sum += stem[index] * element;
			});
			if (checkDigit == checkTable[sum % 11]) 
			{
				return true;
			}
			else 
			{
				return(false);
			}
		} 
		else 
		{
			return(false);
		}
	}

	function GetMod(Value1,Value2)
	{
		var Result = String(Value1 / Value2);
		if (Result.indexOf(".") >= 0)
		{
			Reslut = Result.substring(0,Result.indexOf("."));
		}
		return(parseInt(Value1) - (parseInt(Result) * Value2));
	}

	function GetBarcode(Barcode)
	{
		var i, Even, Odd;
		i = Even = Odd = 0;
		for (i = 1; i <= 11; i += 2)
			Even += parseInt(Barcode.substring(i,i + 1));
		Even *= 3;
		for (i = 0; i <= 10; i += 2)
			Odd += parseInt(Barcode.substring(i,i + 1));
		var Total = Odd + Even;
		return(Barcode.substring(0,12) + (10 - GetMod(Total,10)));
	}

	function ShowFloat(Value,Flag,Round)
	{// Written By : Mohammad Kaiser Anwar
		if (Round === undefined)
		{
			Round = 2;
		}
		if (isNaN(Value))
			return(Value);
		else
		{
			var Str = new String(Value);
			if (Str == "0")
				Str = "0.00";
			else if (Str.indexOf(".") < 0)
				Str = Str + ".00";
			else
			{
				var Value = Str.split(".");
				if (Value[0].length <= 0) Value[0] = "0";
				if (Value[1].length <= Round)
				{
					for (var i = Value[1].length; i < Round; i++)
					{
						Value[1] = Value[1] + "0";
					}
				}
				else
				{
					var Point = 0;
					if (parseInt(Value[1].charAt(Round)) >= 5)
					{
						Point = parseFloat(1.00 / Math.pow(10,Round));
					}
					Point = parseFloat(0+"."+Value[1].substring(0,Round)) + parseFloat(Point);
					Point = Point + "";
					Str = parseFloat(Value[0]) + parseFloat(Point.substring(0,Round+2));
				}
			}
			if (Flag == 1)
				return(parseFloat(Str));
			else if (Flag == 3 && parseFloat(Str) >= 0)
				return("+" + Str);
			else
				return(Str);
		}
	}

	function GetDateStamp(DateTime)
	{
		var DatePart, DateDate, DateTime;
		DatePart = DateTime.split(" ");
		DateDate = DatePart[0].split("-");
		DateTime = DatePart[1].split(":");
		return(Date.UTC(DateDate[0],DateDate[1] - 1,DateDate[2],DateTime[0],DateTime[1],DateTime[2]));
	}

	function GetDates(Date1,Date2)
	{
		var DatePart, DateDate, DateTime;
		var Seconds = 0;
		DatePart = Date1.split(" ");
		DateDate = DatePart[0].split("-");
		DateTime = DatePart[1].split(":");
		Date1 = new Array(DateDate[0],DateDate[1] - 1,DateDate[2],DateTime[0],DateTime[1],DateTime[2]);
		DatePart = Date2.split(" ");
		DateDate = DatePart[0].split("-");
		DateTime = DatePart[1].split(":");
		Date2 = new Array(DateDate[0],DateDate[1] - 1,DateDate[2],DateTime[0],DateTime[1],DateTime[2]);
		Seconds += (Date1[2] - Date2[2]) * 24 * 60 * 60;
		Seconds += (Date1[3] - Date2[3]) * 60 * 60;
		Seconds += (Date1[4] - Date2[4]) * 60;
		Seconds += (Date1[5] - Date2[5]);
		return(Seconds);
	}

	function IsDate(Day,Month,Year,Empty)
	{// Written By : Mohammad Kaiser Anwar
		Day   = String(Day);
		Month = String(Month);
		Year  = String(Year);
		if (Day.indexOf(0) == "0" && Day.length == 2)
		{
			Day = parseInt(Day.substring(1));
		}
		if (Month.indexOf(0) == "0" && Month.length == 2)
		{
			Month = parseInt(Month.substring(1));
		}
		var Flag1 = false;
		var Flag2 = false;
		if (Day == "0" || Month == "0" || Year == "0") Flag1 = true;
		if (Day != "0" || Month != "0" || Year != "0") Flag2 = true;
		if (Flag1 == true && Flag2 == true)
		{
			return(false);
		}
		else if (Flag1 = true && Flag2 == false)
		{
			if (Empty == false)
				return(false);
			else
				return(true);
		}
		else if (Flag1 == false && Flag2 == true)
		{
			var Now = new Date(Year,Month-1,Day);
			if (Now.getDate() == Day && Now.getMonth() == (Month-1) && Now.getFullYear() == Year)
				return(true);
			else
				return(false);
		}
		else
		{
			return(true);
		}
	}

	function IsFullDate(TxtDate,Empty)
	{
		if (TxtDate == "")
		{
			if (Empty == false)
				return(false);
			else
				return(true);
		}
		TxtDate = ReplaceChar(TxtDate," ","");
		if (!(TxtDate.charAt(2) == "/" && TxtDate.charAt(5) == "/" && TxtDate.length == 10))
		{
			return(false);
		}
		return(IsDate(TxtDate.substring(0,2),TxtDate.substring(3,5),TxtDate.substring(6,10),Empty));
	}

	function CompareDate(Date1,Month1,Year1,Date2,Month2,Year2)
	{// Written By : Mohammad Kaiser Anwar
		if (IsDate(Date1,Month1,Year1,1) == false || IsDate(Date2,Month2,Year2,1) == false)
		{
			alert("Invalid Date Formats");
			return(true);
		}
		else
		{
			var Date1 = new Date(Year1,Month1-1,Date1,0,0,0);
			var Date2 = new Date(Year2,Month2-1,Date2,0,0,0);
			if (Date1.getFullYear() > Date2.getFullYear()) return(1);
			if (Date1.getFullYear() == Date2.getFullYear())
			{
				if (Date1.getMonth() > Date2.getMonth()) return(1);
				if (Date1.getMonth() == Date2.getMonth())
				{
					if (Date1.getDate() > Date2.getDate()) return(1);
					if (Date1.getDate() == Date2.getDate()) return(0);
				}
			}
			return(-1);
		}
	}

	function CompareTime(Date1,Date2)
	{// Written By : Mohammad Kaiser Anwar
		// Date 1
		Date1  = Date1.split(" ");
		var MyDate = Date1[0].split("-");
		var MyTime = Date1[1].split(":");
		Date1 = new Date(MyDate[0],MyDate[1],MyDate[2],MyTime[0],MyTime[1],MyTime[2],0);
		// Date 2
		Date2  = Date2.split(" ");
		MyDate = Date2[0].split("-");
		MyTime = Date2[1].split(":");
		Date2 = new Date(MyDate[0],MyDate[1],MyDate[2],MyTime[0],MyTime[1],MyTime[2],0);
		/* Date Comparison */
		if (Date1.getYear() > Date2.getYear()) return (1);
		if (Date1.getYear() == Date2.getYear())
		{
			if (Date1.getMonth() > Date2.getMonth()) return (1);
			if (Date1.getMonth() == Date2.getMonth())
			{
				if (Date1.getDate() > Date2.getDate()) return (1);
				if (Date1.getDate() == Date2.getDate())
				{
					if (Date1.getHours() > Date2.getHours()) return(1);
					if (Date1.getHours() == Date2.getHours())
					{
						if (Date1.getMinutes() > Date2.getMinutes()) return(1);
						if (Date1.getMinutes() == Date2.getMinutes())
						{
							if (Date1.getSeconds() > Date2.getSeconds()) return(1)
							if (Date1.getSeconds() == Date2.getSeconds()) return(0);
						}
					}
				}
			}
		}
		return (-1);
	}

	function ReplaceChar(Text,Search,Replace)
	{// Written By : Mohammad Kaiser Anwar
		Text = new String(Text);
		Text = Text.split(Search);
		return(Text.join(Replace));
	}

	function CheckDelete(ChkArray,Message)
	{// Written By : Mohammad Kaiser Anwar
		if (isNaN(document.Form.elements[ChkArray+'[]'].length))
		{
			if (document.Form.elements[ChkArray+'[]'].checked == true)
				return(confirm("Are You Sure You Want To Delete Selected " + Message + " ?"));
		}
		else
		{
			for (var i = 0; i < parseInt(document.Form.elements[ChkArray+'[]'].length); i++)
			{
				if (document.Form.elements[ChkArray+'[]'][i].checked == true)
					return(confirm("Are You Sure You Want To Delete Selected " + Message + " ?"));
			}
		}
		alert("Please Select At Least One " + Message + " To Delete !");
		return(false);
	}

	function CheckJPG(Form,ElemName)
	{// Written By : Mohammad Kaiser Anwar
		var Picture;
		Picture = document.forms[Form].elements[ElemName].value;
		if (Picture.lastIndexOf(".") > 0)
		{
			var Extension = Picture.substr(Picture.lastIndexOf(".")).toUpperCase();
			if (Extension == ".JPG" || Extension == ".JPEG" )
				return true;
			else
				return false;
		}
	}

	function CheckPNG(Form,ElemName)
	{// Written By : Mohammad Kaiser Anwar
		var Picture;
		Picture = document.forms[Form].elements[ElemName].value;
		if (Picture.lastIndexOf(".") > 0)
		{
			var Extension = Picture.substr(Picture.lastIndexOf(".")).toUpperCase();
			if (Extension == ".PNG")
				return true;
			else
				return false;
		}
	}

	function CheckFile(Form,ElemName,Extension)
	{// Written By : Mohammad Kaiser Anwar
		var Picture =  document.forms[Form].elements[ElemName].value;
		if (Picture.lastIndexOf(".") > 0)
		{
			if (Picture.substr(Picture.lastIndexOf(".")).toUpperCase() == "." + Extension.toUpperCase())
				return true;
			else
				return false;
		}
	}

	function SelectBooking(EventID)
	{
		var SlotID = EventID.substring(12,13);
		document.Form.txtFunctionVenue.value = EventID.substring(0,10);
		document.Form.txtFunctionDate.value  = EventID.substring(0,10);
		if (SlotID == 1 || SlotID == 2)
		{
			if (SlotID == 1)
			{
				document.Form.txtFunctionHall.value = "1";
			}
			else
			{
				document.Form.txtFunctionHall.value = "2";
			}
			document.Form.txtFunctionVenue.value += "      Hall-"+document.Form.txtFunctionHall.value;
			document.Form.txtFunctionVenue.value += "      12:00";
			document.Form.txtFunctionDate.value  += " 12:00:00";
		}
		else
		{
			if (SlotID == 3)
			{
				document.Form.txtFunctionHall.value = "1";
			}
			else
			{
				document.Form.txtFunctionHall.value = "2";
			}
			document.Form.txtFunctionVenue.value += "      Hall-"+document.Form.txtFunctionHall.value;
			document.Form.txtFunctionVenue.value += "      18:00";
			document.Form.txtFunctionDate.value  += " 18:00:00";
		}
	}

	function CheckName(Name)
	{
		if (IsEmpty(Name) == true)
		{
			return(false);
		}
		const Keywords = ['banned','ban','b a n'];
		Name = Name.toLowerCase();
		if (Keywords.includes(Name))
		{
			return false;
		}
	}
	
	function IsName(Text)
	{
		if (Text.Length < 3)
		{
			return("Name Cannot Be Less Than 3 Characters");
		}
		RegExName = /^([a-zA-Z])([a-zA-Z\s\.\-\'])+([a-zA-Z])$/;
		if (RegExName.test(Text) == false)
		{
			return("Only English Alphabets Space Dash & Dot Are Allowed");
		}
		var RegExDuplicate = /([a-z])\1\1+/;       
		if (RegExDuplicate.test(Text))
		{
			return("Alphabets Cannot Come More Than 3 Times Consecutively");
		}
		const RegExBogus = ['abc','xyz','jkl','hjk','mnb','mnbv','hjkl','fds','test','dummy','demo','blah','testing','fuck','none','qwe','qwer',
			'qwert','qwerty','asd','asdf','asdfg','asdfgh','asdfghj','asdfghjkl','zxc','zxcv','zxcvb','zxcvbn','zxcvbnm'];
		if (RegExBogus.includes(Text.toLowerCase()))
		{
			return("It Seems You Have Entered A Fake Name");
		}
		return("");
	}