package uk.gov.beis.pageobjects;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.helper.ScenarioContext;

import uk.gov.beis.pageobjects.AdvicePageObjects.*;
import uk.gov.beis.pageobjects.AuthorityPageObjects.*;
import uk.gov.beis.pageobjects.DeviationRequestPageObjects.*;
import uk.gov.beis.pageobjects.EnforcementNoticePageObjects.*;
import uk.gov.beis.pageobjects.GeneralEnquiryPageObjects.*;
import uk.gov.beis.pageobjects.HomePageLinkPageObjects.*;
import uk.gov.beis.pageobjects.InspectionPlanFeedbackPageObjects.*;
import uk.gov.beis.pageobjects.InspectionPlanPageObjects.*;
import uk.gov.beis.pageobjects.LegalEntityPageObjects.*;
import uk.gov.beis.pageobjects.NewsLetterSubscriptionPageObjects.*;
import uk.gov.beis.pageobjects.OrganisationPageObjects.*;
import uk.gov.beis.pageobjects.OtherPageObjects.*;
import uk.gov.beis.pageobjects.PartnershipPageObjects.*;
import uk.gov.beis.pageobjects.SharedPageObjects.*;
import uk.gov.beis.pageobjects.TransferPartnerships.*;
import uk.gov.beis.pageobjects.UserDashboardPageObjects.*;
import uk.gov.beis.pageobjects.UserManagement.*;

public class WebsiteManager {
	
	public WebDriver driver;
	
	// PAR Home Page
	public HomePage homePage;
	public LocalRegulationPrimaryAuthorityPage localRegulationPrimaryAuthorityPage;
	public PrimaryAuthorityDocumentsPage primaryAuthorityDocumentsPage;
	public TermsAndConditionsPage termsAndConditionsPage;
	public CookiesPage cookiesPage;
	public OPSSPrivacyNoticePage opssPrivacyNoticePage;
	public AccessibilityStatementPage accessibilityStatementPage;
	public OpenGovernmentLicencePage openGovernmentLicencePage;
	public CrownCopyrightPage crownCopyrightPage;

	// Login
	public LoginPage loginPage;
	public PasswordPage passwordPage;
	public MailLogPage mailLogPage;

	// Dash-board
	public DashboardPage dashboardPage;
	public HelpDeskDashboardPage helpDeskDashboardPage;

	// Statistics
	public PARReportingPage parReportingPage;

	// User Management
	public ManagePeoplePage managePeoplePage;
	public ContactDetailsPage contactDetailsPage;
	public UserProfilePage userProfilePage;
	public GiveUserAccountPage giveUserAccountPage;
	public UserMembershipPage userMembershipPage;
	public UserRoleTypePage userTypePage;
	public ProfileReviewPage profileReviewPage;
	public ProfileCompletionPage profileCompletionPage;
	public ChoosePersonToAddPage choosePersonToAddPage;
	public AddMembershipConfirmationPage addMembershipConfirmationPage;

	// Contact Record
	public ContactRecordsPage contactRecordsPage;
	public ContactCommunicationPreferencesPage contactCommunicationPreferencesPage;
	public ContactUpdateSubscriptionPage contactUpdateSubscriptionPage;

	// Legal Entity
	public LegalEntityTypePage legalEntityTypePage;
	public LegalEntityReviewPage legalEntityReviewPage;
	public UpdateLegalEntityPage updateLegalEntityPage;
	public ConfirmThisAmendmentPage confirmThisAmendmentPage;
	public AmendmentCompletedPage amendmentCompletedPage;

	// Partnership
	public PartnershipTermsPage parPartnershipTermsPage;
	public PartnershipTypePage parPartnershipTypePage;
	public PartnershipDescriptionPage parPartnershipDescriptionPage;
	public RegulatoryFunctionPage regulatoryFunctionPage;
	public NumberOfEmployeesPage employeesPage;
	public CheckPartnershipInformationPage checkPartnershipInformationPage;
	public PartnershipInformationPage partnershipInformationPage;
	public PartnershipCompletionPage parPartnershipCompletionPage;
	public PartnershipApprovalPage partnershipApprovalPage;
	public PartnershipRevokedPage partnershipRevokedPage;
	public PartnershipRestoredPage partnershipRestoredPage;

	// Coordinated Partnership
	public MemberListPage memberListPage;
	public MembersListTypePage membersListTypePage;
	public MemberListCountPage memberListCountPage;
	public MembersListUpToDatePage membersListUpToDatePage;
	public UploadListOfMembersPage uploadListOfMembersPage;
	public ConfirmMemberUploadPage confirmMemberUploadPage;
	public MemberListUploadedPage memberListUploadedPage;
	public InspectionPlanCoveragePage inspectionPlanCoveragePage;
	public MemberOrganisationSummaryPage memberOrganisationSummaryPage;
	public MemberOrganisationAddedConfirmationPage memberOrganisationAddedConfirmationPage;
	public MembershipCeasedPage membershipCeasedPage;

	// Partnerships Transfer
	public AuthorityTransferSelectionPage authorityTransferSelectionPage;
	public PartnershipMigrationSelectionPage partnershipMigrationSelectionPage;
	public ConfirmThisTranferPage confirmThisTranferPage;
	public TransferCompletedPage transferCompletedPage;

	// Authority
	public AuthoritiesSearchPage authoritiesSearchPage;
	public AuthorityPage parAuthorityPage;
	public AuthorityNamePage authorityNamePage;
	public ONSCodePage onsCodePage;
	public AuthorityConfirmationPage authorityConfirmationPage;
	public AuthorityAddressDetailsPage authorityAddressDetailsPage;
	public AuthorityTypePage authorityTypePage;

	// Business
	public OrganisationsSearchPage organisationsSearchPage;
	public BusinessNamePage businessNamePage;
	public AddOrganisationNamePage addOrganisationNamePage;
	public AboutTheOrganisationPage aboutTheOrganisationPage;
	public SICCodePage sicCodePage;
	public TradingPage tradingPage;
	public BusinessDetailsPage businessDetailsPage;

	// Search Pages
	public PublicRegistrySearchPage publicRegistrySearchPage;
	public PartnershipSearchPage partnershipSearchPage;
	public PartnershipAdvancedSearchPage partnershipAdvancedSearchPage;
	public InspectionPlanSearchPage inspectionPlanSearchPage;
	public AdviceNoticeSearchPage adviceNoticeSearchPage;
	public EnforcementSearchPage enforcementSearchPage;
	public DeviationSearchPage deviationSearchPage;
	public InspectionFeedbackSearchPage inspectionFeedbackSearchPage;
	public EnquiriesSearchPage enquiriesSearchPage;

	// Inspection Plan
	public UploadInspectionPlanPage uploadInspectionPlanPage;
	public InspectionPlanReviewPage inspectionPlanReviewPage;
	public InspectionPlanDetailsPage inspectionPlanDetailsPage;

	// Advice
	public UploadAdviceNoticePage uploadAdviceNoticePage;
	public AdviceNoticeDetailsPage adviceNoticeDetailsPage;
	public AdviceArchivePage adviceArchivePage;

	// Enforcement Notice
	public ProposedEnforcementPage proposedEnforcementPage;
	public EnforcementReviewPage enforcementReviewPage;
	public EnforcementNotificationPage enforcementNotificationPage;
	public EnforcementCompletionPage enforcementCompletionPage;
	public EnforcementActionPage enforcementActionPage;
	public EnforcementDetailsPage enforcementDetailsPage;
	public EnforceLegalEntityPage enforceLegalEntityPage;
	public EnforcementOfficerContactDetailsPage enforcementOfficerContactDetailsPage;
	public RemoveEnforcementPage removeEnforcementPage;

	// Deviation Request
	public RequestDeviationPage requestDeviationPage;
	public DeviationCompletionPage deviationCompletionPage;
	public DeviationReviewPage deviationReviewPage;
	public DeviationApprovalPage deviationApprovalPage;
	public ReplyDeviationRequestPage replyDeviationRequestPage;

	// Inspection Plan Feedback
	public InspectionFeedbackDetailsPage inspectionFeedbackDetailsPage;
	public InspectionFeedbackConfirmationPage inspectionFeedbackConfirmationPage;
	public InspectionFeedbackCompletionPage inspectionFeedbackCompletionPage;
	public ReplyInspectionFeedbackPage replyInspectionFeedbackPage;

	// General Enquiry
	public RequestEnquiryPage requestEnquiryPage;
	public EnquiryCompletionPage enquiryCompletionPage;
	public EnquiryReviewPage enquiryReviewPage;
	public ReplyEnquiryPage replyEnquiryPage;

	// PAR News Letter
	public NewsLetterSubscriptionPage newsLetterSubscriptionPage;
	public NewsLetterManageSubscriptionListPage newsLetterManageSubscriptionListPage;
	public NewsLetterSubscriptionReviewPage newsLetterSubscriptionReviewPage;

	// Shared Pages
	public DeclarationPage declarationPage;
	public AddAddressPage addAddressPage;
	public AccountInvitePage accountInvitePage;
	public EnterTheDatePage enterTheDatePage;
	public CompletionPage completionPage;
	public RevokePage revokePage;
	public ReinstatePage reinstatePage;
	public BlockPage blockPage;
	public RemovePage removePage;
	public DeletePage deletePage;
	public ChooseAnInspectionPlanPage chooseAnInspectionPlanPage;
	public BasePageObject basePageObject;
	
	public WebsiteManager() {
		
		driver = ScenarioContext.lastDriver;
		
		// PAR Home Page
		homePage = PageFactory.initElements(driver, HomePage.class);
		localRegulationPrimaryAuthorityPage = PageFactory.initElements(driver, LocalRegulationPrimaryAuthorityPage.class);
		primaryAuthorityDocumentsPage = PageFactory.initElements(driver, PrimaryAuthorityDocumentsPage.class);
		termsAndConditionsPage = PageFactory.initElements(driver, TermsAndConditionsPage.class);
		cookiesPage = PageFactory.initElements(driver, CookiesPage.class);
		opssPrivacyNoticePage = PageFactory.initElements(driver, OPSSPrivacyNoticePage.class);
		accessibilityStatementPage = PageFactory.initElements(driver, AccessibilityStatementPage.class);
		openGovernmentLicencePage = PageFactory.initElements(driver, OpenGovernmentLicencePage.class);
		crownCopyrightPage = PageFactory.initElements(driver, CrownCopyrightPage.class);

		// Login
		loginPage = PageFactory.initElements(driver, LoginPage.class);
		passwordPage = PageFactory.initElements(driver, PasswordPage.class);
		mailLogPage = PageFactory.initElements(driver, MailLogPage.class);

		// Dash-board
		dashboardPage = PageFactory.initElements(driver, DashboardPage.class);
		helpDeskDashboardPage = PageFactory.initElements(driver, HelpDeskDashboardPage.class);

		// Statistics
		parReportingPage = PageFactory.initElements(driver, PARReportingPage.class);

		// User Management
		profileReviewPage = PageFactory.initElements(driver, ProfileReviewPage.class);
		profileCompletionPage = PageFactory.initElements(driver, ProfileCompletionPage.class);
		contactDetailsPage = PageFactory.initElements(driver, ContactDetailsPage.class);
		managePeoplePage = PageFactory.initElements(driver, ManagePeoplePage.class);
		userMembershipPage = PageFactory.initElements(driver, UserMembershipPage.class);
		giveUserAccountPage = PageFactory.initElements(driver, GiveUserAccountPage.class);
		userTypePage = PageFactory.initElements(driver, UserRoleTypePage.class);
		userProfilePage = PageFactory.initElements(driver, UserProfilePage.class);
		choosePersonToAddPage = PageFactory.initElements(driver, ChoosePersonToAddPage.class);
		addMembershipConfirmationPage = PageFactory.initElements(driver, AddMembershipConfirmationPage.class);

		// Contact Record
		contactUpdateSubscriptionPage = PageFactory.initElements(driver, ContactUpdateSubscriptionPage.class);
		contactRecordsPage = PageFactory.initElements(driver, ContactRecordsPage.class);
		contactCommunicationPreferencesPage = PageFactory.initElements(driver, ContactCommunicationPreferencesPage.class);

		// Legal Entity
		legalEntityTypePage = PageFactory.initElements(driver, LegalEntityTypePage.class);
		legalEntityReviewPage = PageFactory.initElements(driver, LegalEntityReviewPage.class);
		updateLegalEntityPage = PageFactory.initElements(driver, UpdateLegalEntityPage.class);
		confirmThisAmendmentPage = PageFactory.initElements(driver, ConfirmThisAmendmentPage.class);
		amendmentCompletedPage = PageFactory.initElements(driver, AmendmentCompletedPage.class);

		// Partnership
		partnershipInformationPage = PageFactory.initElements(driver, PartnershipInformationPage.class);
		parPartnershipTypePage = PageFactory.initElements(driver, PartnershipTypePage.class);
		parPartnershipDescriptionPage = PageFactory.initElements(driver, PartnershipDescriptionPage.class);
		declarationPage = PageFactory.initElements(driver, DeclarationPage.class);
		employeesPage = PageFactory.initElements(driver, NumberOfEmployeesPage.class);
		partnershipRestoredPage = PageFactory.initElements(driver, PartnershipRestoredPage.class);
		partnershipRevokedPage = PageFactory.initElements(driver, PartnershipRevokedPage.class);
		partnershipApprovalPage = PageFactory.initElements(driver, PartnershipApprovalPage.class);
		regulatoryFunctionPage = PageFactory.initElements(driver, RegulatoryFunctionPage.class);
		parPartnershipCompletionPage = PageFactory.initElements(driver, PartnershipCompletionPage.class);
		parPartnershipTermsPage = PageFactory.initElements(driver, PartnershipTermsPage.class);
		checkPartnershipInformationPage = PageFactory.initElements(driver, CheckPartnershipInformationPage.class);

		// Coordinated Partnership
		memberListPage = PageFactory.initElements(driver, MemberListPage.class);
		membersListTypePage = PageFactory.initElements(driver, MembersListTypePage.class);
		memberListCountPage = PageFactory.initElements(driver, MemberListCountPage.class);
		membersListUpToDatePage = PageFactory.initElements(driver, MembersListUpToDatePage.class);
		membershipCeasedPage = PageFactory.initElements(driver, MembershipCeasedPage.class);
		uploadListOfMembersPage = PageFactory.initElements(driver, UploadListOfMembersPage.class);
		confirmMemberUploadPage = PageFactory.initElements(driver, ConfirmMemberUploadPage.class);
		memberListUploadedPage = PageFactory.initElements(driver, MemberListUploadedPage.class);
		inspectionPlanCoveragePage = PageFactory.initElements(driver, InspectionPlanCoveragePage.class);
		memberOrganisationSummaryPage = PageFactory.initElements(driver, MemberOrganisationSummaryPage.class);
		memberOrganisationAddedConfirmationPage = PageFactory.initElements(driver, MemberOrganisationAddedConfirmationPage.class);

		// Partnerships Transfer
		authorityTransferSelectionPage = PageFactory.initElements(driver, AuthorityTransferSelectionPage.class);
		partnershipMigrationSelectionPage = PageFactory.initElements(driver, PartnershipMigrationSelectionPage.class);
		confirmThisTranferPage = PageFactory.initElements(driver, ConfirmThisTranferPage.class);
		transferCompletedPage = PageFactory.initElements(driver, TransferCompletedPage.class);

		// Authority
		authoritiesSearchPage = PageFactory.initElements(driver, AuthoritiesSearchPage.class);
		parAuthorityPage = PageFactory.initElements(driver, AuthorityPage.class);
		authorityTypePage = PageFactory.initElements(driver, AuthorityTypePage.class);
		authorityAddressDetailsPage = PageFactory.initElements(driver, AuthorityAddressDetailsPage.class);
		authorityNamePage = PageFactory.initElements(driver, AuthorityNamePage.class);
		onsCodePage = PageFactory.initElements(driver, ONSCodePage.class);
		authorityConfirmationPage = PageFactory.initElements(driver, AuthorityConfirmationPage.class);

		// Business
		organisationsSearchPage = PageFactory.initElements(driver, OrganisationsSearchPage.class);
		addOrganisationNamePage = PageFactory.initElements(driver, AddOrganisationNamePage.class);
		tradingPage = PageFactory.initElements(driver, TradingPage.class);
		sicCodePage = PageFactory.initElements(driver, SICCodePage.class);
		aboutTheOrganisationPage = PageFactory.initElements(driver, AboutTheOrganisationPage.class);
		businessNamePage = PageFactory.initElements(driver, BusinessNamePage.class);
		businessDetailsPage = PageFactory.initElements(driver, BusinessDetailsPage.class);

		// Search Pages
		publicRegistrySearchPage = PageFactory.initElements(driver, PublicRegistrySearchPage.class);
		partnershipSearchPage = PageFactory.initElements(driver, PartnershipSearchPage.class);
		partnershipAdvancedSearchPage = PageFactory.initElements(driver, PartnershipAdvancedSearchPage.class);
		inspectionPlanSearchPage = PageFactory.initElements(driver, InspectionPlanSearchPage.class);
		adviceNoticeSearchPage = PageFactory.initElements(driver, AdviceNoticeSearchPage.class);
		enforcementSearchPage = PageFactory.initElements(driver, EnforcementSearchPage.class);
		deviationSearchPage = PageFactory.initElements(driver, DeviationSearchPage.class);
		inspectionFeedbackSearchPage = PageFactory.initElements(driver, InspectionFeedbackSearchPage.class);
		enquiriesSearchPage = PageFactory.initElements(driver, EnquiriesSearchPage.class);

		// Inspection Plan
		uploadInspectionPlanPage = PageFactory.initElements(driver, UploadInspectionPlanPage.class);
		inspectionPlanDetailsPage = PageFactory.initElements(driver, InspectionPlanDetailsPage.class);
		inspectionPlanReviewPage = PageFactory.initElements(driver, InspectionPlanReviewPage.class);

		// Advice
		adviceNoticeDetailsPage = PageFactory.initElements(driver, AdviceNoticeDetailsPage.class);
		uploadAdviceNoticePage = PageFactory.initElements(driver, UploadAdviceNoticePage.class);
		adviceArchivePage = PageFactory.initElements(driver, AdviceArchivePage.class);

		// Enforcement Notice
		proposedEnforcementPage = PageFactory.initElements(driver, ProposedEnforcementPage.class);
		enforcementActionPage = PageFactory.initElements(driver, EnforcementActionPage.class);
		enforcementDetailsPage = PageFactory.initElements(driver, EnforcementDetailsPage.class);
		enforceLegalEntityPage = PageFactory.initElements(driver, EnforceLegalEntityPage.class);
		enforcementNotificationPage = PageFactory.initElements(driver, EnforcementNotificationPage.class);
		enforcementReviewPage = PageFactory.initElements(driver, EnforcementReviewPage.class);
		enforcementCompletionPage = PageFactory.initElements(driver, EnforcementCompletionPage.class);
		removeEnforcementPage = PageFactory.initElements(driver, RemoveEnforcementPage.class);

		// Deviation Request
		requestDeviationPage = PageFactory.initElements(driver, RequestDeviationPage.class);
		deviationReviewPage = PageFactory.initElements(driver, DeviationReviewPage.class);
		deviationCompletionPage = PageFactory.initElements(driver, DeviationCompletionPage.class);
		deviationApprovalPage = PageFactory.initElements(driver, DeviationApprovalPage.class);
		replyDeviationRequestPage = PageFactory.initElements(driver, ReplyDeviationRequestPage.class);

		// Inspection Plan Feedback
		inspectionFeedbackDetailsPage = PageFactory.initElements(driver, InspectionFeedbackDetailsPage.class);
		inspectionFeedbackCompletionPage = PageFactory.initElements(driver, InspectionFeedbackCompletionPage.class);
		inspectionFeedbackConfirmationPage = PageFactory.initElements(driver, InspectionFeedbackConfirmationPage.class);
		replyInspectionFeedbackPage = PageFactory.initElements(driver, ReplyInspectionFeedbackPage.class);

		// General Enquiry
		requestEnquiryPage = PageFactory.initElements(driver, RequestEnquiryPage.class);
		enquiryReviewPage = PageFactory.initElements(driver, EnquiryReviewPage.class);
		enquiryCompletionPage = PageFactory.initElements(driver, EnquiryCompletionPage.class);
		replyEnquiryPage = PageFactory.initElements(driver, ReplyEnquiryPage.class);

		// PAR News Letter
		newsLetterSubscriptionPage = PageFactory.initElements(driver, NewsLetterSubscriptionPage.class);
		newsLetterManageSubscriptionListPage = PageFactory.initElements(driver, NewsLetterManageSubscriptionListPage.class);
		newsLetterSubscriptionReviewPage = PageFactory.initElements(driver, NewsLetterSubscriptionReviewPage.class);

		// Shared Pages
		enforcementOfficerContactDetailsPage = PageFactory.initElements(driver, EnforcementOfficerContactDetailsPage.class);
		addAddressPage = PageFactory.initElements(driver, AddAddressPage.class);
		accountInvitePage = PageFactory.initElements(driver, AccountInvitePage.class);
		enterTheDatePage = PageFactory.initElements(driver, EnterTheDatePage.class);
		completionPage = PageFactory.initElements(driver, CompletionPage.class);
		revokePage = PageFactory.initElements(driver, RevokePage.class);
		reinstatePage = PageFactory.initElements(driver, ReinstatePage.class);
		blockPage = PageFactory.initElements(driver, BlockPage.class);
		deletePage = PageFactory.initElements(driver, DeletePage.class);
		removePage = PageFactory.initElements(driver, RemovePage.class);
		chooseAnInspectionPlanPage = PageFactory.initElements(driver, ChooseAnInspectionPlanPage.class);
		basePageObject = PageFactory.initElements(driver, BasePageObject.class);
	}
}
