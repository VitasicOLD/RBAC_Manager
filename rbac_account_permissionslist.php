<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "includes/framework/ewcfg9.php" ?>
<?php include_once "includes/framework/ewmysql9.php" ?>
<?php include_once "phpfn9.php" ?>
<?php include_once "rbac_account_permissionsinfo.php" ?>
<?php include_once "userfn9.php" ?>
<?php

//
// Page class
//

$rbac_account_permissions_list = NULL; // Initialize page object first

class crbac_account_permissions_list extends crbac_account_permissions {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{94C0E450-F9A8-47EE-A905-551040DB9277}";

	// Table name
	var $TableName = 'rbac_account_permissions';

	// Page object name
	var $PageObjName = 'rbac_account_permissions_list';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Page URLs
	var $AddUrl;
	var $EditUrl;
	var $CopyUrl;
	var $DeleteUrl;
	var $ViewUrl;
	var $ListUrl;

	// Export URLs
	var $ExportPrintUrl;
	var $ExportHtmlUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportXmlUrl;
	var $ExportCsvUrl;
	var $ExportPdfUrl;

	// Update URLs
	var $InlineAddUrl;
	var $InlineCopyUrl;
	var $InlineEditUrl;
	var $GridAddUrl;
	var $GridEditUrl;
	var $MultiDeleteUrl;
	var $MultiUpdateUrl;

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			$html .= "<p class=\"ewMessage\">" . $sMessage . "</p>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			$html .= "<table class=\"ewMessageTable\"><tr><td class=\"ewWarningIcon\"></td><td class=\"ewWarningMessage\">" . $sWarningMessage . "</td></tr></table>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			$html .= "<table class=\"ewMessageTable\"><tr><td class=\"ewSuccessIcon\"></td><td class=\"ewSuccessMessage\">" . $sSuccessMessage . "</td></tr></table>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			$html .= "<table class=\"ewMessageTable\"><tr><td class=\"ewErrorIcon\"></td><td class=\"ewErrorMessage\">" . $sErrorMessage . "</td></tr></table>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p class=\"phpmaker\">" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Fotoer exists, display
			echo "<p class=\"phpmaker\">" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language, $UserAgent;

		// User agent
		$UserAgent = ew_UserAgent();
		$GLOBALS["Page"] = &$this;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (rbac_account_permissions)
		if (!isset($GLOBALS["rbac_account_permissions"])) {
			$GLOBALS["rbac_account_permissions"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["rbac_account_permissions"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "rbac_account_permissionsadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "rbac_account_permissionsdelete.php";
		$this->MultiUpdateUrl = "rbac_account_permissionsupdate.php";

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'rbac_account_permissions', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();

		// List options
		$this->ListOptions = new cListOptions();
		$this->ListOptions->TableVar = $this->TableVar;

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "span";
		$this->ExportOptions->TagClassName = "ewExportOption";
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// Security
		$Security = new cAdvancedSecurity();
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		if (!$Security->IsLoggedIn()) {
			$Security->SaveLastUrl();
			$this->Page_Terminate("login.php");
		}

		// Get export parameters
		if (@$_GET["export"] <> "") {
			$this->Export = $_GET["export"];
		} elseif (ew_IsHttpPost()) {
			if (@$_POST["exporttype"] <> "")
				$this->Export = $_POST["exporttype"];
		} else {
			$this->setExportReturnUrl(ew_CurrentUrl());
		}
		$gsExport = $this->Export; // Get export parameter, used in header
		$gsExportFile = $this->TableVar; // Get export file, used in header

		// Get grid add count
		$gridaddcnt = @$_GET[EW_TABLE_GRID_ADD_ROW_COUNT];
		if (is_numeric($gridaddcnt) && $gridaddcnt > 0)
			$this->GridAddRowCount = $gridaddcnt;

		// Set up list options
		$this->SetupListOptions();

		// Setup export options
		$this->SetupExportOptions();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"];

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $conn;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();
		$this->Page_Redirecting($url);

		 // Close connection
		$conn->Close();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}

	// Class variables
	var $ListOptions; // List options
	var $ExportOptions; // Export options
	var $DisplayRecs = 30;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $Pager;
	var $SearchWhere = ""; // Search WHERE clause
	var $RecCnt = 0; // Record count
	var $EditRowCnt;
	var $StartRowCnt = 1;
	var $RowCnt = 0;
	var $Attrs = array(); // Row attributes and cell attributes
	var $RowIndex = 0; // Row index
	var $KeyCount = 0; // Key count
	var $RowAction = ""; // Row action
	var $RowOldKey = ""; // Row old key (for copy)
	var $RecPerRow = 0;
	var $ColCnt = 0;
	var $DbMasterFilter = ""; // Master filter
	var $DbDetailFilter = ""; // Detail filter
	var $MasterRecordExists;	
	var $MultiSelectKey;
	var $Command;
	var $Recordset;
	var $OldRecordset;

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError, $gsSearchError, $Security;

		// Search filters
		$sSrchAdvanced = ""; // Advanced search filter
		$sSrchBasic = ""; // Basic search filter
		$sFilter = "";

		// Get command
		$this->Command = strtolower(@$_GET["cmd"]);
		if ($this->IsPageRequest()) { // Validate request

			// Set up records per page
			$this->SetUpDisplayRecs();

			// Handle reset command
			$this->ResetCmd();

			// Hide all options
			if ($this->Export <> "" ||
				$this->CurrentAction == "gridadd" ||
				$this->CurrentAction == "gridedit") {
				$this->ListOptions->HideAllOptions();
				$this->ExportOptions->HideAllOptions();
			}

			// Set up sorting order
			$this->SetUpSortOrder();
		}

		// Restore display records
		if ($this->getRecordsPerPage() <> "") {
			$this->DisplayRecs = $this->getRecordsPerPage(); // Restore from Session
		} else {
			$this->DisplayRecs = 30; // Load default
		}

		// Load Sorting Order
		$this->LoadSortOrder();

		// Build filter
		$sFilter = "";
		ew_AddFilter($sFilter, $this->DbDetailFilter);
		ew_AddFilter($sFilter, $this->SearchWhere);

		// Set up filter in session
		$this->setSessionWhere($sFilter);
		$this->CurrentFilter = "";

		// Export data only
		if (in_array($this->Export, array("html","word","excel","xml","csv","email","pdf"))) {
			$this->ExportData();
			if ($this->Export == "email")
				$this->Page_Terminate($this->ExportReturnUrl());
			else
				$this->Page_Terminate(); // Terminate response
			exit();
		}
	}

	// Set up number of records displayed per page
	function SetUpDisplayRecs() {
		$sWrk = @$_GET[EW_TABLE_REC_PER_PAGE];
		if ($sWrk <> "") {
			if (is_numeric($sWrk)) {
				$this->DisplayRecs = intval($sWrk);
			} else {
				if (strtolower($sWrk) == "all") { // Display all records
					$this->DisplayRecs = -1;
				} else {
					$this->DisplayRecs = 30; // Non-numeric, load default
				}
			}
			$this->setRecordsPerPage($this->DisplayRecs); // Save to Session

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Build filter for all keys
	function BuildKeyFilter() {
		global $objForm;
		$sWrkFilter = "";

		// Update row index and get row key
		$rowindex = 1;
		$objForm->Index = $rowindex;
		$sThisKey = strval($objForm->GetValue("k_key"));
		while ($sThisKey <> "") {
			if ($this->SetupKeyValues($sThisKey)) {
				$sFilter = $this->KeyFilter();
				if ($sWrkFilter <> "") $sWrkFilter .= " OR ";
				$sWrkFilter .= $sFilter;
			} else {
				$sWrkFilter = "0=1";
				break;
			}

			// Update row index and get row key
			$rowindex++; // next row
			$objForm->Index = $rowindex;
			$sThisKey = strval($objForm->GetValue("k_key"));
		}
		return $sWrkFilter;
	}

	// Set up key values
	function SetupKeyValues($key) {
		$arrKeyFlds = explode($GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"], $key);
		if (count($arrKeyFlds) >= 3) {
			$this->accountId->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->accountId->FormValue))
				return FALSE;
			$this->permissionId->setFormValue($arrKeyFlds[1]);
			if (!is_numeric($this->permissionId->FormValue))
				return FALSE;
			$this->realmId->setFormValue($arrKeyFlds[2]);
			if (!is_numeric($this->realmId->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->accountId); // accountId
			$this->UpdateSort($this->permissionId); // permissionId
			$this->UpdateSort($this->granted); // granted
			$this->UpdateSort($this->realmId); // realmId
			$this->setStartRecordNumber(1); // Reset start position
		}
	}

	// Load sort order parameters
	function LoadSortOrder() {
		$sOrderBy = $this->getSessionOrderBy(); // Get ORDER BY from Session
		if ($sOrderBy == "") {
			if ($this->SqlOrderBy() <> "") {
				$sOrderBy = $this->SqlOrderBy();
				$this->setSessionOrderBy($sOrderBy);
			}
		}
	}

	// Reset command
	// cmd=reset (Reset search parameters)
	// cmd=resetall (Reset search and master/detail parameters)
	// cmd=resetsort (Reset sort parameters)
	function ResetCmd() {

		// Check if reset command
		if (substr($this->Command,0,5) == "reset") {

			// Reset sorting order
			if ($this->Command == "resetsort") {
				$sOrderBy = "";
				$this->setSessionOrderBy($sOrderBy);
				$this->setSessionOrderByList($sOrderBy);
				$this->accountId->setSort("");
				$this->permissionId->setSort("");
				$this->granted->setSort("");
				$this->realmId->setSort("");
			}

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Set up list options
	function SetupListOptions() {
		global $Security, $Language;

		// "view"
		$item = &$this->ListOptions->Add("view");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->IsLoggedIn();
		$item->OnLeft = TRUE;

		// "edit"
		$item = &$this->ListOptions->Add("edit");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->IsLoggedIn();
		$item->OnLeft = TRUE;

		// "copy"
		$item = &$this->ListOptions->Add("copy");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->IsLoggedIn();
		$item->OnLeft = TRUE;

		// "checkbox"
		$item = &$this->ListOptions->Add("checkbox");
		$item->CssStyle = "white-space: nowrap; text-align: center; vertical-align: middle; margin: 0px;";
		$item->Visible = $Security->IsLoggedIn();
		$item->OnLeft = TRUE;
		$item->Header = "<input type=\"checkbox\" name=\"key\" id=\"key\" class=\"phpmaker\" onclick=\"ew_SelectAllKey(this);\">";
		$item->MoveTo(0);

		// Call ListOptions_Load event
		$this->ListOptions_Load();
	}

	// Render list options
	function RenderListOptions() {
		global $Security, $Language, $objForm;
		$this->ListOptions->LoadDefault();

		// "view"
		$oListOpt = &$this->ListOptions->Items["view"];
		if ($Security->IsLoggedIn())
			$oListOpt->Body = "<a class=\"ewRowLink\" href=\"" . $this->ViewUrl . "\">" . $Language->Phrase("ViewLink") . "</a>";

		// "edit"
		$oListOpt = &$this->ListOptions->Items["edit"];
		if ($Security->IsLoggedIn()) {
			$oListOpt->Body = "<a class=\"ewRowLink\" href=\"" . $this->EditUrl . "\">" . $Language->Phrase("EditLink") . "</a>";
		}

		// "copy"
		$oListOpt = &$this->ListOptions->Items["copy"];
		if ($Security->IsLoggedIn()) {
			$oListOpt->Body = "<a class=\"ewRowLink\" href=\"" . $this->CopyUrl . "\">" . $Language->Phrase("CopyLink") . "</a>";
		}

		// "checkbox"
		$oListOpt = &$this->ListOptions->Items["checkbox"];
		if ($Security->IsLoggedIn())
			$oListOpt->Body = "<input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->accountId->CurrentValue . $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"] . $this->permissionId->CurrentValue . $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"] . $this->realmId->CurrentValue) . "\" class=\"phpmaker\" onclick='ew_ClickMultiCheckbox(event, this);'>";
		$this->RenderListOptionsExt();

		// Call ListOptions_Rendered event
		$this->ListOptions_Rendered();
	}

	function RenderListOptionsExt() {
		global $Security, $Language;
	}

	// Set up starting record parameters
	function SetUpStartRec() {
		if ($this->DisplayRecs == 0)
			return;
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET[EW_TABLE_START_REC] <> "") { // Check for "start" parameter
				$this->StartRec = $_GET[EW_TABLE_START_REC];
				$this->setStartRecordNumber($this->StartRec);
			} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
				$PageNo = $_GET[EW_TABLE_PAGE_NO];
				if (is_numeric($PageNo)) {
					$this->StartRec = ($PageNo-1)*$this->DisplayRecs+1;
					if ($this->StartRec <= 0) {
						$this->StartRec = 1;
					} elseif ($this->StartRec >= intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1) {
						$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1;
					}
					$this->setStartRecordNumber($this->StartRec);
				}
			}
		}
		$this->StartRec = $this->getStartRecordNumber();

		// Check if correct start record counter
		if (!is_numeric($this->StartRec) || $this->StartRec == "") { // Avoid invalid start record counter
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} elseif (intval($this->StartRec) > intval($this->TotalRecs)) { // Avoid starting record > total records
			$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to last page first record
			$this->setStartRecordNumber($this->StartRec);
		} elseif (($this->StartRec-1) % $this->DisplayRecs <> 0) {
			$this->StartRec = intval(($this->StartRec-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to page boundary
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Load recordset
	function LoadRecordset($offset = -1, $rowcnt = -1) {
		global $conn;

		// Call Recordset Selecting event
		$this->Recordset_Selecting($this->CurrentFilter);

		// Load List page SQL
		$sSql = $this->SelectSQL();
		if ($offset > -1 && $rowcnt > -1)
			$sSql .= " LIMIT $rowcnt OFFSET $offset";

		// Load recordset
		$rs = ew_LoadRecordset($sSql);

		// Call Recordset Selected event
		$this->Recordset_Selected($rs);
		return $rs;
	}

	// Load row based on key values
	function LoadRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		global $conn;
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
		$this->accountId->setDbValue($rs->fields('accountId'));
		$this->permissionId->setDbValue($rs->fields('permissionId'));
		if (array_key_exists('EV__permissionId', $rs->fields)) {
			$this->permissionId->VirtualValue = $rs->fields('EV__permissionId'); // Set up virtual field value
		} else {
			$this->permissionId->VirtualValue = ""; // Clear value
		}
		$this->granted->setDbValue($rs->fields('granted'));
		$this->realmId->setDbValue($rs->fields('realmId'));
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("accountId")) <> "")
			$this->accountId->CurrentValue = $this->getKey("accountId"); // accountId
		else
			$bValidKey = FALSE;
		if (strval($this->getKey("permissionId")) <> "")
			$this->permissionId->CurrentValue = $this->getKey("permissionId"); // permissionId
		else
			$bValidKey = FALSE;
		if (strval($this->getKey("realmId")) <> "")
			$this->realmId->CurrentValue = $this->getKey("realmId"); // realmId
		else
			$bValidKey = FALSE;

		// Load old recordset
		if ($bValidKey) {
			$this->CurrentFilter = $this->KeyFilter();
			$sSql = $this->SQL();
			$this->OldRecordset = ew_LoadRecordset($sSql);
			$this->LoadRowValues($this->OldRecordset); // Load row values
		} else {
			$this->OldRecordset = NULL;
		}
		return $bValidKey;
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		$this->ViewUrl = $this->GetViewUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->InlineEditUrl = $this->GetInlineEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->InlineCopyUrl = $this->GetInlineCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
		// accountId
		// permissionId
		// granted
		// realmId

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// accountId
			if (strval($this->accountId->CurrentValue) <> "") {
				$sFilterWrk = "`id`" . ew_SearchString("=", $this->accountId->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `id`, `id` AS `DispFld`, `username` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `account`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->accountId->ViewValue = $rswrk->fields('DispFld');
					$this->accountId->ViewValue .= ew_ValueSeparator(1,$this->accountId) . $rswrk->fields('Disp2Fld');
					$rswrk->Close();
				} else {
					$this->accountId->ViewValue = $this->accountId->CurrentValue;
				}
			} else {
				$this->accountId->ViewValue = NULL;
			}
			$this->accountId->ViewCustomAttributes = "";

			// permissionId
			if ($this->permissionId->VirtualValue <> "") {
				$this->permissionId->ViewValue = $this->permissionId->VirtualValue;
			} else {
			if (strval($this->permissionId->CurrentValue) <> "") {
				$sFilterWrk = "`id`" . ew_SearchString("=", $this->permissionId->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `id`, `id` AS `DispFld`, `name` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `rbac_permissions`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->permissionId->ViewValue = $rswrk->fields('DispFld');
					$this->permissionId->ViewValue .= ew_ValueSeparator(1,$this->permissionId) . $rswrk->fields('Disp2Fld');
					$rswrk->Close();
				} else {
					$this->permissionId->ViewValue = $this->permissionId->CurrentValue;
				}
			} else {
				$this->permissionId->ViewValue = NULL;
			}
			}
			$this->permissionId->ViewCustomAttributes = "";

			// granted
			if (strval($this->granted->CurrentValue) <> "") {
				switch ($this->granted->CurrentValue) {
					case $this->granted->FldTagValue(1):
						$this->granted->ViewValue = $this->granted->FldTagCaption(1) <> "" ? $this->granted->FldTagCaption(1) : $this->granted->CurrentValue;
						break;
					case $this->granted->FldTagValue(2):
						$this->granted->ViewValue = $this->granted->FldTagCaption(2) <> "" ? $this->granted->FldTagCaption(2) : $this->granted->CurrentValue;
						break;
					default:
						$this->granted->ViewValue = $this->granted->CurrentValue;
				}
			} else {
				$this->granted->ViewValue = NULL;
			}
			$this->granted->ViewCustomAttributes = "";

			// realmId
			if (strval($this->realmId->CurrentValue) <> "") {
				$sFilterWrk = "`id`" . ew_SearchString("=", $this->realmId->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `id`, `id` AS `DispFld`, `name` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `realmlist`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->realmId->ViewValue = $rswrk->fields('DispFld');
					$this->realmId->ViewValue .= ew_ValueSeparator(1,$this->realmId) . $rswrk->fields('Disp2Fld');
					$rswrk->Close();
				} else {
					$this->realmId->ViewValue = $this->realmId->CurrentValue;
				}
			} else {
				$this->realmId->ViewValue = NULL;
			}
			$this->realmId->ViewCustomAttributes = "";

			// accountId
			$this->accountId->LinkCustomAttributes = "";
			$this->accountId->HrefValue = "";
			$this->accountId->TooltipValue = "";

			// permissionId
			$this->permissionId->LinkCustomAttributes = "";
			$this->permissionId->HrefValue = "";
			$this->permissionId->TooltipValue = "";

			// granted
			$this->granted->LinkCustomAttributes = "";
			$this->granted->HrefValue = "";
			$this->granted->TooltipValue = "";

			// realmId
			$this->realmId->LinkCustomAttributes = "";
			$this->realmId->HrefValue = "";
			$this->realmId->TooltipValue = "";
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Set up export options
	function SetupExportOptions() {
		global $Language;

		// Printer friendly
		$item = &$this->ExportOptions->Add("print");
		$item->Body = "<a href=\"" . $this->ExportPrintUrl . "\">" . $Language->Phrase("PrinterFriendly") . "</a>";
		$item->Visible = TRUE;

		// Export to Excel
		$item = &$this->ExportOptions->Add("excel");
		$item->Body = "<a href=\"" . $this->ExportExcelUrl . "\">" . $Language->Phrase("ExportToExcel") . "</a>";
		$item->Visible = FALSE;

		// Export to Word
		$item = &$this->ExportOptions->Add("word");
		$item->Body = "<a href=\"" . $this->ExportWordUrl . "\">" . $Language->Phrase("ExportToWord") . "</a>";
		$item->Visible = FALSE;

		// Export to Html
		$item = &$this->ExportOptions->Add("html");
		$item->Body = "<a href=\"" . $this->ExportHtmlUrl . "\">" . $Language->Phrase("ExportToHtml") . "</a>";
		$item->Visible = TRUE;

		// Export to Xml
		$item = &$this->ExportOptions->Add("xml");
		$item->Body = "<a href=\"" . $this->ExportXmlUrl . "\">" . $Language->Phrase("ExportToXml") . "</a>";
		$item->Visible = FALSE;

		// Export to Csv
		$item = &$this->ExportOptions->Add("csv");
		$item->Body = "<a href=\"" . $this->ExportCsvUrl . "\">" . $Language->Phrase("ExportToCsv") . "</a>";
		$item->Visible = FALSE;

		// Export to Pdf
		$item = &$this->ExportOptions->Add("pdf");
		$item->Body = "<a href=\"" . $this->ExportPdfUrl . "\">" . $Language->Phrase("ExportToPDF") . "</a>";
		$item->Visible = FALSE;

		// Export to Email
		$item = &$this->ExportOptions->Add("email");
		$item->Body = "<a id=\"emf_rbac_account_permissions\" href=\"javascript:void(0);\" onclick=\"ew_EmailDialogShow({lnk:'emf_rbac_account_permissions',hdr:ewLanguage.Phrase('ExportToEmail'),f:document.frbac_account_permissionslist,sel:false});\">" . $Language->Phrase("ExportToEmail") . "</a>";
		$item->Visible = FALSE;

		// Hide options for export/action
		if ($this->Export <> "" || $this->CurrentAction <> "")
			$this->ExportOptions->HideAllOptions();
	}

	// Export data in HTML/CSV/Word/Excel/XML/Email/PDF format
	function ExportData() {
		$utf8 = (strtolower(EW_CHARSET) == "utf-8");
		$bSelectLimit = EW_SELECT_LIMIT;

		// Load recordset
		if ($bSelectLimit) {
			$this->TotalRecs = $this->SelectRecordCount();
		} else {
			if ($rs = $this->LoadRecordset())
				$this->TotalRecs = $rs->RecordCount();
		}
		$this->StartRec = 1;

		// Export all
		if ($this->ExportAll) {
			set_time_limit(EW_EXPORT_ALL_TIME_LIMIT);
			$this->DisplayRecs = $this->TotalRecs;
			$this->StopRec = $this->TotalRecs;
		} else { // Export one page only
			$this->SetUpStartRec(); // Set up start record position

			// Set the last record to display
			if ($this->DisplayRecs <= 0) {
				$this->StopRec = $this->TotalRecs;
			} else {
				$this->StopRec = $this->StartRec + $this->DisplayRecs - 1;
			}
		}
		if ($bSelectLimit)
			$rs = $this->LoadRecordset($this->StartRec-1, $this->DisplayRecs <= 0 ? $this->TotalRecs : $this->DisplayRecs);
		if (!$rs) {
			header("Content-Type:"); // Remove header
			header("Content-Disposition:");
			$this->ShowMessage();
			return;
		}
		$ExportDoc = ew_ExportDocument($this, "h");
		$ParentTable = "";
		if ($bSelectLimit) {
			$StartRec = 1;
			$StopRec = $this->DisplayRecs <= 0 ? $this->TotalRecs : $this->DisplayRecs;
		} else {
			$StartRec = $this->StartRec;
			$StopRec = $this->StopRec;
		}
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		$ExportDoc->Text .= $sHeader;
		$this->ExportDocument($ExportDoc, $rs, $StartRec, $StopRec, "");
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		$ExportDoc->Text .= $sFooter;

		// Close recordset
		$rs->Close();

		// Export header and footer
		$ExportDoc->ExportHeaderAndFooter();

		// Clean output buffer
		if (!EW_DEBUG_ENABLED && ob_get_length())
			ob_end_clean();

		// Write debug message if enabled
		if (EW_DEBUG_ENABLED)
			echo ew_DebugMsg();

		// Output data
		$ExportDoc->Export();
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}

	// ListOptions Load event
	function ListOptions_Load() {

		// Example:
		//$opt = &$this->ListOptions->Add("new");
		//$opt->Header = "xxx";
		//$opt->OnLeft = TRUE; // Link on left
		//$opt->MoveTo(0); // Move to first column

	}

	// ListOptions Rendered event
	function ListOptions_Rendered() {

		// Example: 
		//$this->ListOptions->Items["new"]->Body = "xxx";

	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($rbac_account_permissions_list)) $rbac_account_permissions_list = new crbac_account_permissions_list();

// Page init
$rbac_account_permissions_list->Page_Init();

// Page main
$rbac_account_permissions_list->Page_Main();
?>
<?php include_once "header.php" ?>
<?php if ($rbac_account_permissions->Export == "") { ?>
<script type="text/javascript">

// Page object
var rbac_account_permissions_list = new ew_Page("rbac_account_permissions_list");
rbac_account_permissions_list.PageID = "list"; // Page ID
var EW_PAGE_ID = rbac_account_permissions_list.PageID; // For backward compatibility

// Form object
var frbac_account_permissionslist = new ew_Form("frbac_account_permissionslist");

// Form_CustomValidate event
frbac_account_permissionslist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
frbac_account_permissionslist.ValidateRequired = true;
<?php } else { ?>
frbac_account_permissionslist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
frbac_account_permissionslist.Lists["x_accountId"] = {"LinkField":"x_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_id","x_username","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
frbac_account_permissionslist.Lists["x_permissionId"] = {"LinkField":"x_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_id","x_name","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
frbac_account_permissionslist.Lists["x_realmId"] = {"LinkField":"x_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_id","x_name","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		$rbac_account_permissions_list->TotalRecs = $rbac_account_permissions->SelectRecordCount();
	} else {
		if ($rbac_account_permissions_list->Recordset = $rbac_account_permissions_list->LoadRecordset())
			$rbac_account_permissions_list->TotalRecs = $rbac_account_permissions_list->Recordset->RecordCount();
	}
	$rbac_account_permissions_list->StartRec = 1;
	if ($rbac_account_permissions_list->DisplayRecs <= 0 || ($rbac_account_permissions->Export <> "" && $rbac_account_permissions->ExportAll)) // Display all records
		$rbac_account_permissions_list->DisplayRecs = $rbac_account_permissions_list->TotalRecs;
	if (!($rbac_account_permissions->Export <> "" && $rbac_account_permissions->ExportAll))
		$rbac_account_permissions_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$rbac_account_permissions_list->Recordset = $rbac_account_permissions_list->LoadRecordset($rbac_account_permissions_list->StartRec-1, $rbac_account_permissions_list->DisplayRecs);
?>
<p style="white-space: nowrap;"><span id="ewPageCaption" class="ewTitle ewTableTitle"><?php echo $Language->Phrase("TblTypeTABLE") ?><?php echo $rbac_account_permissions->TableCaption() ?>&nbsp;&nbsp;</span>
<?php $rbac_account_permissions_list->ExportOptions->Render("body"); ?>
</p>
<?php $rbac_account_permissions_list->ShowPageHeader(); ?>
<?php
$rbac_account_permissions_list->ShowMessage();
?>
<br>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<form name="frbac_account_permissionslist" id="frbac_account_permissionslist" class="ewForm" action="" method="post">
<input type="hidden" name="t" value="rbac_account_permissions">
<div id="gmp_rbac_account_permissions" class="ewGridMiddlePanel">
<?php if ($rbac_account_permissions_list->TotalRecs > 0) { ?>
<table id="tbl_rbac_account_permissionslist" class="ewTable ewTableSeparate">
<?php echo $rbac_account_permissions->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$rbac_account_permissions_list->RenderListOptions();

// Render list options (header, left)
$rbac_account_permissions_list->ListOptions->Render("header", "left");
?>
<?php if ($rbac_account_permissions->accountId->Visible) { // accountId ?>
	<?php if ($rbac_account_permissions->SortUrl($rbac_account_permissions->accountId) == "") { ?>
		<td><span id="elh_rbac_account_permissions_accountId" class="rbac_account_permissions_accountId"><table class="ewTableHeaderBtn"><thead><tr><td><?php echo $rbac_account_permissions->accountId->FldCaption() ?></td></tr></thead></table></span></td>
	<?php } else { ?>
		<td><div onmousedown="ew_Sort(event,'<?php echo $rbac_account_permissions->SortUrl($rbac_account_permissions->accountId) ?>',1);"><span id="elh_rbac_account_permissions_accountId" class="rbac_account_permissions_accountId">
			<table class="ewTableHeaderBtn"><thead><tr><td class="ewTableHeaderCaption"><?php echo $rbac_account_permissions->accountId->FldCaption() ?></td><td class="ewTableHeaderSort"><?php if ($rbac_account_permissions->accountId->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" alt="" style="border: 0;"><?php } elseif ($rbac_account_permissions->accountId->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" alt="" style="border: 0;"><?php } ?></td></tr></thead></table>
		</span></div></td>		
	<?php } ?>
<?php } ?>		
<?php if ($rbac_account_permissions->permissionId->Visible) { // permissionId ?>
	<?php if ($rbac_account_permissions->SortUrl($rbac_account_permissions->permissionId) == "") { ?>
		<td><span id="elh_rbac_account_permissions_permissionId" class="rbac_account_permissions_permissionId"><table class="ewTableHeaderBtn"><thead><tr><td><?php echo $rbac_account_permissions->permissionId->FldCaption() ?></td></tr></thead></table></span></td>
	<?php } else { ?>
		<td><div onmousedown="ew_Sort(event,'<?php echo $rbac_account_permissions->SortUrl($rbac_account_permissions->permissionId) ?>',1);"><span id="elh_rbac_account_permissions_permissionId" class="rbac_account_permissions_permissionId">
			<table class="ewTableHeaderBtn"><thead><tr><td class="ewTableHeaderCaption"><?php echo $rbac_account_permissions->permissionId->FldCaption() ?></td><td class="ewTableHeaderSort"><?php if ($rbac_account_permissions->permissionId->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" alt="" style="border: 0;"><?php } elseif ($rbac_account_permissions->permissionId->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" alt="" style="border: 0;"><?php } ?></td></tr></thead></table>
		</span></div></td>		
	<?php } ?>
<?php } ?>		
<?php if ($rbac_account_permissions->granted->Visible) { // granted ?>
	<?php if ($rbac_account_permissions->SortUrl($rbac_account_permissions->granted) == "") { ?>
		<td><span id="elh_rbac_account_permissions_granted" class="rbac_account_permissions_granted"><table class="ewTableHeaderBtn"><thead><tr><td><?php echo $rbac_account_permissions->granted->FldCaption() ?></td></tr></thead></table></span></td>
	<?php } else { ?>
		<td><div onmousedown="ew_Sort(event,'<?php echo $rbac_account_permissions->SortUrl($rbac_account_permissions->granted) ?>',1);"><span id="elh_rbac_account_permissions_granted" class="rbac_account_permissions_granted">
			<table class="ewTableHeaderBtn"><thead><tr><td class="ewTableHeaderCaption"><?php echo $rbac_account_permissions->granted->FldCaption() ?></td><td class="ewTableHeaderSort"><?php if ($rbac_account_permissions->granted->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" alt="" style="border: 0;"><?php } elseif ($rbac_account_permissions->granted->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" alt="" style="border: 0;"><?php } ?></td></tr></thead></table>
		</span></div></td>		
	<?php } ?>
<?php } ?>		
<?php if ($rbac_account_permissions->realmId->Visible) { // realmId ?>
	<?php if ($rbac_account_permissions->SortUrl($rbac_account_permissions->realmId) == "") { ?>
		<td><span id="elh_rbac_account_permissions_realmId" class="rbac_account_permissions_realmId"><table class="ewTableHeaderBtn"><thead><tr><td><?php echo $rbac_account_permissions->realmId->FldCaption() ?></td></tr></thead></table></span></td>
	<?php } else { ?>
		<td><div onmousedown="ew_Sort(event,'<?php echo $rbac_account_permissions->SortUrl($rbac_account_permissions->realmId) ?>',1);"><span id="elh_rbac_account_permissions_realmId" class="rbac_account_permissions_realmId">
			<table class="ewTableHeaderBtn"><thead><tr><td class="ewTableHeaderCaption"><?php echo $rbac_account_permissions->realmId->FldCaption() ?></td><td class="ewTableHeaderSort"><?php if ($rbac_account_permissions->realmId->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" alt="" style="border: 0;"><?php } elseif ($rbac_account_permissions->realmId->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" alt="" style="border: 0;"><?php } ?></td></tr></thead></table>
		</span></div></td>		
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$rbac_account_permissions_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($rbac_account_permissions->ExportAll && $rbac_account_permissions->Export <> "") {
	$rbac_account_permissions_list->StopRec = $rbac_account_permissions_list->TotalRecs;
} else {

	// Set the last record to display
	if ($rbac_account_permissions_list->TotalRecs > $rbac_account_permissions_list->StartRec + $rbac_account_permissions_list->DisplayRecs - 1)
		$rbac_account_permissions_list->StopRec = $rbac_account_permissions_list->StartRec + $rbac_account_permissions_list->DisplayRecs - 1;
	else
		$rbac_account_permissions_list->StopRec = $rbac_account_permissions_list->TotalRecs;
}
$rbac_account_permissions_list->RecCnt = $rbac_account_permissions_list->StartRec - 1;
if ($rbac_account_permissions_list->Recordset && !$rbac_account_permissions_list->Recordset->EOF) {
	$rbac_account_permissions_list->Recordset->MoveFirst();
	if (!$bSelectLimit && $rbac_account_permissions_list->StartRec > 1)
		$rbac_account_permissions_list->Recordset->Move($rbac_account_permissions_list->StartRec - 1);
} elseif (!$rbac_account_permissions->AllowAddDeleteRow && $rbac_account_permissions_list->StopRec == 0) {
	$rbac_account_permissions_list->StopRec = $rbac_account_permissions->GridAddRowCount;
}

// Initialize aggregate
$rbac_account_permissions->RowType = EW_ROWTYPE_AGGREGATEINIT;
$rbac_account_permissions->ResetAttrs();
$rbac_account_permissions_list->RenderRow();
while ($rbac_account_permissions_list->RecCnt < $rbac_account_permissions_list->StopRec) {
	$rbac_account_permissions_list->RecCnt++;
	if (intval($rbac_account_permissions_list->RecCnt) >= intval($rbac_account_permissions_list->StartRec)) {
		$rbac_account_permissions_list->RowCnt++;

		// Set up key count
		$rbac_account_permissions_list->KeyCount = $rbac_account_permissions_list->RowIndex;

		// Init row class and style
		$rbac_account_permissions->ResetAttrs();
		$rbac_account_permissions->CssClass = "";
		if ($rbac_account_permissions->CurrentAction == "gridadd") {
		} else {
			$rbac_account_permissions_list->LoadRowValues($rbac_account_permissions_list->Recordset); // Load row values
		}
		$rbac_account_permissions->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$rbac_account_permissions->RowAttrs = array_merge($rbac_account_permissions->RowAttrs, array('data-rowindex'=>$rbac_account_permissions_list->RowCnt, 'id'=>'r' . $rbac_account_permissions_list->RowCnt . '_rbac_account_permissions', 'data-rowtype'=>$rbac_account_permissions->RowType));

		// Render row
		$rbac_account_permissions_list->RenderRow();

		// Render list options
		$rbac_account_permissions_list->RenderListOptions();
?>
	<tr<?php echo $rbac_account_permissions->RowAttributes() ?>>
<?php

// Render list options (body, left)
$rbac_account_permissions_list->ListOptions->Render("body", "left", $rbac_account_permissions_list->RowCnt);
?>
	<?php if ($rbac_account_permissions->accountId->Visible) { // accountId ?>
		<td<?php echo $rbac_account_permissions->accountId->CellAttributes() ?>><span id="el<?php echo $rbac_account_permissions_list->RowCnt ?>_rbac_account_permissions_accountId" class="rbac_account_permissions_accountId">
<span<?php echo $rbac_account_permissions->accountId->ViewAttributes() ?>>
<?php echo $rbac_account_permissions->accountId->ListViewValue() ?></span>
</span><a id="<?php echo $rbac_account_permissions_list->PageObjName . "_row_" . $rbac_account_permissions_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($rbac_account_permissions->permissionId->Visible) { // permissionId ?>
		<td<?php echo $rbac_account_permissions->permissionId->CellAttributes() ?>><span id="el<?php echo $rbac_account_permissions_list->RowCnt ?>_rbac_account_permissions_permissionId" class="rbac_account_permissions_permissionId">
<span<?php echo $rbac_account_permissions->permissionId->ViewAttributes() ?>>
<?php echo $rbac_account_permissions->permissionId->ListViewValue() ?></span>
</span><a id="<?php echo $rbac_account_permissions_list->PageObjName . "_row_" . $rbac_account_permissions_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($rbac_account_permissions->granted->Visible) { // granted ?>
		<td<?php echo $rbac_account_permissions->granted->CellAttributes() ?>><span id="el<?php echo $rbac_account_permissions_list->RowCnt ?>_rbac_account_permissions_granted" class="rbac_account_permissions_granted">
<span<?php echo $rbac_account_permissions->granted->ViewAttributes() ?>>
<?php echo $rbac_account_permissions->granted->ListViewValue() ?></span>
</span><a id="<?php echo $rbac_account_permissions_list->PageObjName . "_row_" . $rbac_account_permissions_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($rbac_account_permissions->realmId->Visible) { // realmId ?>
		<td<?php echo $rbac_account_permissions->realmId->CellAttributes() ?>><span id="el<?php echo $rbac_account_permissions_list->RowCnt ?>_rbac_account_permissions_realmId" class="rbac_account_permissions_realmId">
<span<?php echo $rbac_account_permissions->realmId->ViewAttributes() ?>>
<?php echo $rbac_account_permissions->realmId->ListViewValue() ?></span>
</span><a id="<?php echo $rbac_account_permissions_list->PageObjName . "_row_" . $rbac_account_permissions_list->RowCnt ?>"></a></td>
	<?php } ?>
<?php

// Render list options (body, right)
$rbac_account_permissions_list->ListOptions->Render("body", "right", $rbac_account_permissions_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($rbac_account_permissions->CurrentAction <> "gridadd")
		$rbac_account_permissions_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($rbac_account_permissions->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($rbac_account_permissions_list->Recordset)
	$rbac_account_permissions_list->Recordset->Close();
?>
<?php if ($rbac_account_permissions->Export == "") { ?>
<div class="ewGridLowerPanel">
<?php if ($rbac_account_permissions->CurrentAction <> "gridadd" && $rbac_account_permissions->CurrentAction <> "gridedit") { ?>
<form name="ewpagerform" id="ewpagerform" class="ewForm" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager"><tr><td>
<?php if (!isset($rbac_account_permissions_list->Pager)) $rbac_account_permissions_list->Pager = new cPrevNextPager($rbac_account_permissions_list->StartRec, $rbac_account_permissions_list->DisplayRecs, $rbac_account_permissions_list->TotalRecs) ?>
<?php if ($rbac_account_permissions_list->Pager->RecordCount > 0) { ?>
	<table cellspacing="0" class="ewStdTable"><tbody><tr><td><span class="phpmaker"><?php echo $Language->Phrase("Page") ?>&nbsp;</span></td>
<!--first page button-->
	<?php if ($rbac_account_permissions_list->Pager->FirstButton->Enabled) { ?>
	<td><a href="<?php echo $rbac_account_permissions_list->PageUrl() ?>start=<?php echo $rbac_account_permissions_list->Pager->FirstButton->Start ?>"><img src="images/first.gif" alt="<?php echo $Language->Phrase("PagerFirst") ?>" width="16" height="16" style="border: 0;"></a></td>
	<?php } else { ?>
	<td><img src="images/firstdisab.gif" alt="<?php echo $Language->Phrase("PagerFirst") ?>" width="16" height="16" style="border: 0;"></td>
	<?php } ?>
<!--previous page button-->
	<?php if ($rbac_account_permissions_list->Pager->PrevButton->Enabled) { ?>
	<td><a href="<?php echo $rbac_account_permissions_list->PageUrl() ?>start=<?php echo $rbac_account_permissions_list->Pager->PrevButton->Start ?>"><img src="images/prev.gif" alt="<?php echo $Language->Phrase("PagerPrevious") ?>" width="16" height="16" style="border: 0;"></a></td>
	<?php } else { ?>
	<td><img src="images/prevdisab.gif" alt="<?php echo $Language->Phrase("PagerPrevious") ?>" width="16" height="16" style="border: 0;"></td>
	<?php } ?>
<!--current page number-->
	<td><input type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" id="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $rbac_account_permissions_list->Pager->CurrentPage ?>" size="4"></td>
<!--next page button-->
	<?php if ($rbac_account_permissions_list->Pager->NextButton->Enabled) { ?>
	<td><a href="<?php echo $rbac_account_permissions_list->PageUrl() ?>start=<?php echo $rbac_account_permissions_list->Pager->NextButton->Start ?>"><img src="images/next.gif" alt="<?php echo $Language->Phrase("PagerNext") ?>" width="16" height="16" style="border: 0;"></a></td>	
	<?php } else { ?>
	<td><img src="images/nextdisab.gif" alt="<?php echo $Language->Phrase("PagerNext") ?>" width="16" height="16" style="border: 0;"></td>
	<?php } ?>
<!--last page button-->
	<?php if ($rbac_account_permissions_list->Pager->LastButton->Enabled) { ?>
	<td><a href="<?php echo $rbac_account_permissions_list->PageUrl() ?>start=<?php echo $rbac_account_permissions_list->Pager->LastButton->Start ?>"><img src="images/last.gif" alt="<?php echo $Language->Phrase("PagerLast") ?>" width="16" height="16" style="border: 0;"></a></td>	
	<?php } else { ?>
	<td><img src="images/lastdisab.gif" alt="<?php echo $Language->Phrase("PagerLast") ?>" width="16" height="16" style="border: 0;"></td>
	<?php } ?>
	<td><span class="phpmaker">&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $rbac_account_permissions_list->Pager->PageCount ?></span></td>
	</tr></tbody></table>
	</td>	
	<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td>
	<span class="phpmaker"><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $rbac_account_permissions_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $rbac_account_permissions_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $rbac_account_permissions_list->Pager->RecordCount ?></span>
<?php } else { ?>
	<?php if ($rbac_account_permissions_list->SearchWhere == "0=101") { ?>
	<span class="phpmaker"><?php echo $Language->Phrase("EnterSearchCriteria") ?></span>
	<?php } else { ?>
	<span class="phpmaker"><?php echo $Language->Phrase("NoRecord") ?></span>
	<?php } ?>
<?php } ?>
	</td>
<?php if ($rbac_account_permissions_list->TotalRecs > 0) { ?>
	<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td><table cellspacing="0" class="ewStdTable"><tbody><tr><td><?php echo $Language->Phrase("RecordsPerPage") ?>&nbsp;</td><td>
<input type="hidden" name="t" value="rbac_account_permissions">
<select name="<?php echo EW_TABLE_REC_PER_PAGE ?>" id="<?php echo EW_TABLE_REC_PER_PAGE ?>" onchange="this.form.submit();">
<option value="10"<?php if ($rbac_account_permissions_list->DisplayRecs == 10) { ?> selected="selected"<?php } ?>>10</option>
<option value="20"<?php if ($rbac_account_permissions_list->DisplayRecs == 20) { ?> selected="selected"<?php } ?>>20</option>
<option value="30"<?php if ($rbac_account_permissions_list->DisplayRecs == 30) { ?> selected="selected"<?php } ?>>30</option>
<option value="40"<?php if ($rbac_account_permissions_list->DisplayRecs == 40) { ?> selected="selected"<?php } ?>>40</option>
<option value="50"<?php if ($rbac_account_permissions_list->DisplayRecs == 50) { ?> selected="selected"<?php } ?>>50</option>
<option value="ALL"<?php if ($rbac_account_permissions->getRecordsPerPage() == -1) { ?> selected="selected"<?php } ?>><?php echo $Language->Phrase("AllRecords") ?></option>
</select></td></tr></tbody></table>
	</td>
<?php } ?>
</tr></table>
</form>
<?php } ?>
<span class="phpmaker">
<?php if ($Security->IsLoggedIn()) { ?>
<?php if ($rbac_account_permissions_list->AddUrl <> "") { ?>
<a class="ewGridLink" href="<?php echo $rbac_account_permissions_list->AddUrl ?>"><?php echo $Language->Phrase("AddLink") ?></a>&nbsp;&nbsp;
<?php } ?>
<?php } ?>
<?php if ($rbac_account_permissions_list->TotalRecs > 0) { ?>
<?php if ($Security->IsLoggedIn()) { ?>
<a class="ewGridLink" href="" onclick="ew_SubmitSelected(document.frbac_account_permissionslist, '<?php echo $rbac_account_permissions_list->MultiDeleteUrl ?>');return false;"><?php echo $Language->Phrase("DeleteSelectedLink") ?></a>&nbsp;&nbsp;
<?php } ?>
<?php } ?>
</span>
</div>
<?php } ?>
</td></tr></table>
<?php if ($rbac_account_permissions->Export == "") { ?>
<script type="text/javascript">
frbac_account_permissionslist.Init();
</script>
<?php } ?>
<?php
$rbac_account_permissions_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($rbac_account_permissions->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$rbac_account_permissions_list->Page_Terminate();
?>
