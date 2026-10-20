<!-- #include file="HelperGroupsRulesJS.asp" -->
<!-- #include file="Mail_JS.asp" -->
<% // ASP JavaScript

var WA_Auth_Separator = "|§|";

function WA_AuthenticateUser(WA_Auth_Parameter){

	var UserAuthenticated = false;
	
	var WA_Auth_loginSQL = "SELECT " + WA_Auth_Parameter.sessionColumns.join(',') + " FROM " + WA_Auth_Parameter.tableName + " WHERE ";

	var WA_Auth_login_cmd;
	WA_Auth_login_cmd =  Server.CreateObject("ADODB.Command");
	WA_Auth_login_cmd.ActiveConnection = WA_Auth_Parameter.connection;

	for(var i=0;i<WA_Auth_Parameter.columns.length;i++){
		WA_Auth_loginSQL += ((i>0)?" AND ":" ") + WA_Auth_Parameter.columns[i] + " = ? ";
		WA_Auth_login_cmd.Parameters.Append(WA_Auth_login_cmd.CreateParameter("param"+i, WA_Auth_Parameter.columnTypes[i], 1, WA_Auth_Parameter.columnSizes[i], WA_Auth_Parameter.columnValues[i]));
	}
	
	WA_Auth_login_cmd.CommandText = WA_Auth_loginSQL;
	WA_Auth_login_cmd.Prepared = true;
	var WA_Auth_loginRS = WA_Auth_login_cmd.Execute();
	
	if (!WA_Auth_loginRS.EOF || !WA_Auth_loginRS.BOF) {
		UserAuthenticated = true;
		for(var s = 0;s<WA_Auth_Parameter.sessionColumns.length;s++){
			Session(WA_Auth_Parameter.sessionNames[s]) = String(WA_Auth_loginRS.Fields.Item(WA_Auth_Parameter.sessionColumns[s]).Value);
		}

		if (WA_Auth_Parameter.gotoPreviousURL && String(Request.QueryString("accessdenied")) != "undefined") {
			WA_Auth_Parameter.successRedirect = Request.QueryString("accessdenied");
		}
 
		if(WA_Auth_Parameter.successRedirect!=""){
			WA_Auth_Parameter.successRedirect = WA_Auth_BuildRedirectURL(WA_Auth_Parameter.successRedirect, WA_Auth_Parameter.keepQueryString);
			Response.Redirect(WA_Auth_Parameter.successRedirect);
		}
	}

	if(!UserAuthenticated && WA_Auth_Parameter.failRedirect != ""){
		WA_Auth_Parameter.failRedirect = WA_Auth_BuildRedirectURL(WA_Auth_Parameter.failRedirect, WA_Auth_Parameter.keepQueryString);
		Response.Redirect(WA_Auth_Parameter.failRedirect);
	}
  
}

function WA_Auth_ClearSession(clearAll, clearThese){

	if(clearAll){
		Session.Contents.RemoveAll();
		Session.Abandon();
	}
	else{
		var numItems = clearThese.length;
		for(var i=0;i<numItems;i++){
			Session.Contents.Remove(clearThese[i]);
		}
	}

}

function WA_Auth_RestrictAccess(redirectURL){
	redirectURL = WA_Auth_BuildRedirectURL(redirectURL, false, true);
	Response.Redirect(redirectURL);
}

function WA_Auth_ForgotPassword(WA_Auth_Parameter){
	var selectColumns = new Array();
	for(var i=0;i<WA_Auth_Parameter.selectColumns.length;i++){
		if(WA_Auth_Parameter.selectColumns[i] != ''){
			selectColumns.push(WA_Auth_Parameter.selectColumns[i]);
		}
	}
	selectColumns.push(WA_Auth_Parameter.column);
	selectColumns.push(WA_Auth_Parameter.usernameColumn);
	selectColumns.push(WA_Auth_Parameter.passwordColumn);

	var WA_Auth_Forgot_Recordset_cmd = Server.CreateObject ("ADODB.Command");
	WA_Auth_Forgot_Recordset_cmd.ActiveConnection = WA_Auth_Parameter.connection;
	WA_Auth_Forgot_Recordset_cmd.CommandText = "SELECT " + selectColumns.join(',') + " FROM " + WA_Auth_Parameter.tableName + " WHERE " + WA_Auth_Parameter.column + " = ?";
	WA_Auth_Forgot_Recordset_cmd.Prepared = true;
	WA_Auth_Forgot_Recordset_cmd.Parameters.Append(WA_Auth_Forgot_Recordset_cmd.CreateParameter("param1", WA_Auth_Parameter.columnType, 1, WA_Auth_Parameter.columnSize, WA_Auth_Parameter.columnValue)); // adVarChar

	var WA_Auth_Forgot_Recordset = WA_Auth_Forgot_Recordset_cmd.Execute();

	if (!WA_Auth_Forgot_Recordset.EOF || !WA_Auth_Forgot_Recordset.BOF) {
		WA_Auth_Parameter.toAddress = String(WA_Auth_Forgot_Recordset(WA_Auth_Parameter.column));
		for(var idx=0;idx<selectColumns.length;idx++){
			WA_Auth_Parameter.mailBody = WA_Auth_Parameter.mailBody.replace(new RegExp("\\[" + selectColumns[idx] + "\\]", "g"), WA_Auth_Forgot_Recordset(selectColumns[idx]));
		}
		for(var idx=0;idx<WA_Auth_Parameter.sessionVariables.length;idx++){
			WA_Auth_Parameter.mailBody = WA_Auth_Parameter.mailBody.replace(new RegExp("\\[Session." + WA_Auth_Parameter.sessionVariables[idx] + "\\]", "g"), ((Session(WA_Auth_Parameter.sessionVariables[idx]))?Session(WA_Auth_Parameter.sessionVariables[idx]):""));
		}
		if(WA_Auth_Parameter.fromAddressDisplay != ''){
			WA_Auth_Parameter.fromAddress = WA_Auth_Parameter.fromAddressDisplay + '|WA|' + WA_Auth_Parameter.fromAddress;
		}
		WA_Auth_Parameter.emailFunction(WA_Auth_Parameter);	
		if(WA_Auth_Parameter.successRedirect!=""){
			WA_Auth_Parameter.successRedirect = WA_Auth_BuildRedirectURL(WA_Auth_Parameter.successRedirect, WA_Auth_Parameter.keepQueryString);
			Response.Redirect(WA_Auth_Parameter.successRedirect);
		}
	}
	else{
		if(WA_Auth_Parameter.failRedirect!=""){
			WA_Auth_Parameter.failRedirect = WA_Auth_BuildRedirectURL(WA_Auth_Parameter.failRedirect, WA_Auth_Parameter.keepQueryString);
			Response.Redirect(WA_Auth_Parameter.failRedirect);
		}
	}

}

function WA_Auth_BuildRedirectURL(redirectURL, keepCurrentQueryString, addDeniedURL){

	if (keepCurrentQueryString && redirectURL != "" && Request.QueryString && Request.QueryString.Count > 0)  {
		redirectURL += ((String(redirectURL).indexOf('?') == -1)?"?":"&") + Request.QueryString;
	}
	
	if(addDeniedURL){	
		var WA_Auth_Referrer = Request.ServerVariables("URL");
		if (String(Request.QueryString()).length > 0){
			WA_Auth_Referrer = WA_Auth_Referrer + "?" + String(Request.QueryString());
		}
		redirectURL = redirectURL + ((String(redirectURL).indexOf("?") >= 0)?"&":"?") + "accessdenied=" + Server.URLEncode(WA_Auth_Referrer);
	}

	return redirectURL
}

// Rules functions
function WA_Auth_RulePasses(ruleName){
	var ruleObj = new WA_Auth_RuleObject(ruleName, false);
	return ruleObj.evaluateRules();
}

function WA_AuthComparisonObject(allow, value, operator, compareTo){
	this.allow = allow;
	this.value = value||"";
	this.operator = operator;
	this.compareTo = compareTo;
}

function WA_Auth_RuleObject(ruleName, doDebug){
	this.ruleName = ruleName||"";
	this.doDebug = doDebug||false;
	this.comparisons = WA_Auth_GetComparisonsForRule(ruleName)||[];
	this.evaluateRules = WA_Auth_RuleObject_EvaluateRules;
	this.debugComparison = WA_Auth_RuleObject_Debug;
	this.debugAllComparisons = WA_Auth_RuleObject_DebugAll;
}

function WA_Auth_RuleObject_EvaluateRules(){
	var rulePasses = false;
	var compareLen = this.comparisons.length;
	for(var idx=0;idx<compareLen;idx++){
		var comparison = this.comparisons[idx];
		var compareSucceeds = false;
		switch(comparison.operator){
/*
			1-9		Direct value comparisons
			10-19		String Comparisons
			20-29		List Comparisons
*/
			case 1:
				compareSucceeds = (comparison.value == comparison.compareTo);
				break;
				
			case 2:
				compareSucceeds = (comparison.value != comparison.compareTo);
				break;
				
			case 3:
				compareSucceeds = (comparison.value < comparison.compareTo);
				break;
				
			case 4:
				compareSucceeds = (comparison.value <= comparison.compareTo);
				break;
				
			case 5:
				compareSucceeds = (comparison.value > comparison.compareTo);
				break;
				
			case 6:
				compareSucceeds = (comparison.value >= comparison.compareTo);
				break;
				
			case 20:
				compareSucceeds = WA_Auth_GroupContainsValue(comparison.compareTo, comparison.value);
				break;
		
		}
		// handle restrict if
		if((!comparison.allow && compareSucceeds) || (comparison.allow && !compareSucceeds)){
			rulePasses = false;
			break;
		}
		else if(compareSucceeds){
			rulePasses = true;
			break;
		}
	}
	
	return rulePasses;
}


// Groups functions 
function WA_Auth_GroupContainsValue(groupName, value){
	var group = WA_Auth_GetGroup(groupName);
	var groupLen = group.length;
	var valueFound = false;
	
	for(var idx = 0;idx<groupLen;idx++){
		if(group[idx]==value){
			valueFound = true;
			break;
		}
	}

	return valueFound;
}


// Debug functions
function WA_Auth_RuleObject_DebugAll(){
	if(!this.doDebug) return;
	var n = this.comparisons.length;
	for(var idx=0;idx<n;idx++){
		this.debugComparison(idx);
	}
}

function WA_Auth_RuleObject_Debug(idx){
	if(!this.doDebug) return;
	var arr = [];
	for(var x in this.comparisons[idx]){
		if(typeof this.comparisons[idx][x] != "function"){
			arr.push(x + " : " + this.comparisons[idx][x]);
		}
	}
	Response.Write(arr.join("<br />")+"<br />");
}

%>
<script runat="server" language="vbscript">

Function WA_Auth_SessionDebug()
	Dim SessionString
	SessionString="Session variables: <br />"
	For Each x in Session.Contents
		SessionString = SessionString & x & "=" & Session.Contents(x) & "<br />"
	Next
	Response.Write(SessionString)
	SessionDebug = SessionString
End Function
</script>