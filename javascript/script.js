//From index.php
$(document).ready(function(){
    $(".showReg").click(function(){
        $("#registrationForm").load("userForm.php");
    });
});

$(document).ready(function(){
    $(".hideReg").click(function(){
        $("#registrationForm").empty();
    });
});

//All form validations
function validateLogin()
{
    var username = document.getElementById("username").value;
    var password = document.getElementById("password").value;
    var emptyAttrs = [];
    submitOK = true;
    var i = 0;
    
    if(isEmpty(username))
    {
        emptyAttrs[i++]="Username";
    }
     
    if(isEmpty(password))
    {
        emptyAttrs[i++]="Password";
    }
    
    if(emptyAttrs.length > 0)
    {
        alert(emptyAttrs + " cannot be left blank");
        return false;
    }       
}

function validateRegistration()
{
    var name = document.getElementById("name").value;
    var username = document.getElementById("uname").value;
    var password = document.getElementById("pwd").value;
    var gender = document.getElementById("gender").value;
    var phone = document.getElementById("phone").value;
    var email = document.getElementById("email").value;
    var at = email.indexOf("@");
    var dot = email.lastIndexOf(".");
    var emptyAttrs = [];
    var errorMsg = "";
    submitOK = true;
    var i = 0;
    
    if(isEmpty(name))
    {
        emptyAttrs[i++]="Name";
    }
    
    if(isEmpty(username))
    {
        emptyAttrs[i++]="Username";
    }
     
    if(isEmpty(password))
    {
        emptyAttrs[i++]="Password";
    }
     
    if(isEmpty(gender))
    {
        emptyAttrs[i++]="Gender";
    }
     
    if(isEmpty(email)) 
    {
        emptyAttrs[i++]="E-mail";
    }
    
    if(emptyAttrs.length > 0)
    {
        errorMsg += ">> " + emptyAttrs + " cannot be left blank";
        submitOK = false;
    }
    
    if(phone.length > 0 && (isEmpty(phone) || phone.length != 10 || !isNumber(phone)))
    {
        errorMsg += "\n>> Phone number should be of 10 digits";
        submitOK = false;
    }
    
    if (email.length > 0 && (at == -1 || dot == -1 || at > dot || isEmpty(email.substring(0, at)) || isEmpty(email.substring(at + 1, dot)) || isEmpty(email.substring(dot + 1)))) 
    {
        errorMsg += "\n>> Valid email address should be of the format <string>@<string>.<string>";
        submitOK = false;
    }
    
    if (!submitOK)
    {
        alert(errorMsg);
    }
    
    return submitOK;
}

function validatePoolForm()
{
    var startTime = document.getElementById("input_poolForm_startTime").value;
    var startFrom = document.getElementById("input_poolForm_from").value;
    var upTo = document.getElementById("input_poolForm_to").value;
    var viaArray = document.getElementsByClassName("input_poolForm_via");
    var via = [];
    for(var j=0;j<viaArray.length;j++) {
        via.push(viaArray[j].value);
    }
    var vehType = document.getElementById("input_poolForm_vehicle_type").value;
    var occupancy = document.getElementById("input_poolForm_availability").value;
    var emptyAttrs = [];
    var errorMsg = "";
    submitOK = true;
    var i = 0;
    
    if(isEmpty(startTime))
    {
        emptyAttrs[i++]="'Start Time'";
    }
    
    if(isEmpty(startFrom))
    {
        emptyAttrs[i++]="'From'";
    }
     
    if(isEmpty(upTo))
    {
        emptyAttrs[i++]="'To'";
    }
    
    if(vehType == "select") {
        var vehicle = document.getElementById("input_poolForm_vehicle").value;
        if(isEmpty(vehicle)) 
        {
            emptyAttrs[i++]="'Vehicle'";
        }
    }
    else {
        var model = document.getElementById("input_poolForm_vehicle_model").value;
        var color = document.getElementById("input_poolForm_vehicle_color").value;
        var regNo = document.getElementById("input_poolForm_vehicle_regNo").value;
        var occupancy = document.getElementById("input_poolForm_vehicle_occupancy").value;
        
        if(isEmpty(model)) 
        {
            emptyAttrs[i++]="'Vehicle->Model'";
        }
        
        if(isEmpty(color)) 
        {
            emptyAttrs[i++]="'Vehicle->Color'";
        }
        
        if(isEmpty(regNo)) 
        {
            emptyAttrs[i++]="'Vehicle->Reg #'";
        }
        
        if(isEmpty(occupancy)) 
        {
            emptyAttrs[i++]="'Vehicle->Space'";
        }
    }
    
    if(isEmpty(occupancy))
    {
        emptyAttrs[i++]="'Occupancy'";
    }
    
    if(emptyAttrs.length > 0)
    {
        errorMsg += ">> " + emptyAttrs + " cannot be left blank";
        submitOK = false;
    }
    
    if(!isEmpty(startFrom) && !isEmpty(upTo) && startFrom.localeCompare(upTo) == 0) {
        errorMsg += "\n>> 'From' and 'To' cannot be same.\nYou cannot use this site to book round-trips just for fun\nA mail is sent to you manager to report this";
        submitOK = false;
    }
    
    if(hasDuplicates(via))
    {
        errorMsg += "\n>> 'Via' should not have duplicate locations";
        submitOK = false;
    }
    
    if(!isEmpty(startFrom) && via.indexOf(startFrom) > -1)
    {
        errorMsg += "\n>> 'Via' should not have same location as 'From'";
        submitOK = false;
    }
    
    if(!isEmpty(upTo) && via.indexOf(upTo) > -1)
    {
        errorMsg += "\n>> 'Via' should not have same location as 'To'";
        submitOK = false;
    }
    
    if(vehType != "select") {
        var occupancy = document.getElementById("input_poolForm_vehicle_occupancy").value;
        if(occupancy.length > 0 && (isEmpty(occupancy) || !isNumber(occupancy)))
        {
            errorMsg += "\n>> 'Vehicle->Space' should be a valid number";
            submitOK = false;
        }
    }
    
    if(occupancy.length > 0 && (isEmpty(occupancy) || !isNumber(occupancy)))
    {
        errorMsg += "\n>> 'Occupancy' should be a valid number";
        submitOK = false;
    }
    
    if (!submitOK)
    {
        alert(errorMsg);
    }
    
    return submitOK;
}

function validateVehicleForm()
{
    var model = document.getElementById("input_vehicleForm_model").value;
    var color = document.getElementById("input_vehicleForm_color").value;
    var regNo = document.getElementById("input_vehicleForm_regNo").value;
    var occupancy = document.getElementById("input_vehicleForm_occupancy").value;
    var emptyAttrs = [];
    var errorMsg = "";
    submitOK = true;
    var i = 0;
    
    if(isEmpty(model)) 
    {
        emptyAttrs[i++]="'Model'";
    }
    
    if(isEmpty(color)) 
    {
        emptyAttrs[i++]="'Color'";
    }
    
    if(isEmpty(regNo)) 
    {
        emptyAttrs[i++]="'Registration No'";
    }
    
    if(isEmpty(occupancy)) 
    {
        emptyAttrs[i++]="'Occupancy'";
    }
        
    if(emptyAttrs.length > 0)
    {
        errorMsg += ">> " + emptyAttrs + " cannot be left blank";
        submitOK = false;
    }
    
    if(occupancy.length > 0 && (isEmpty(occupancy) || !isNumber(occupancy)))
    {
        errorMsg += "\n>> 'Occupancy' should be a valid number";
        submitOK = false;
    }
    
    if (!submitOK)
    {
        alert(errorMsg);
    }
    
    return submitOK;
}

function isEmpty(str) {
    return (!str || 0 === str.length || str.replace(/\s/g,"") == "");
}

function isNumber(str) {
    return /^\d+$/.test(str);
}

Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};

function hasDuplicates(arr) {
    var i, len = arr.length, out=[], obj={};
    var lenWOSpace = len;
    for (i=0;i<len;i++) {
        var value = arr[i];
        if(isEmpty(value))
            lenWOSpace--;
        else
            obj[value]=0;
    }
    return (Object.size(obj)<lenWOSpace);
}
