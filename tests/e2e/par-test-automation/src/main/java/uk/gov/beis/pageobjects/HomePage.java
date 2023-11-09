package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.helper.PropertiesUtil;
import uk.gov.beis.helper.ScenarioContext;
import uk.gov.beis.pageobjects.HomePageLinkPageObjects.*;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipSearchPage;

public class HomePage extends BasePageObject {

	@FindBy(linkText = "Sign in")
	private WebElement signinButton;
	
	@FindBy(linkText = "Read more about Primary Authority")
	private WebElement readMorePrimaryAuthorityLink;
	
	@FindBy(linkText = "Access tools and templates for local authorities")
	private WebElement toolsAndTemplatesResourcesLink;
	
	@FindBy(linkText = "Search the public list of partnerships")
	private WebElement searchPartnershipsResourcesLink;
	
	@FindBy(linkText = "Terms and Conditions")
	private WebElement termsAndConditionsLink;
	
	@FindBy(linkText = "Cookies")
	private WebElement cookiesFooterLink;
	
	@FindBy(linkText = "Privacy")
	private WebElement privacyLink;
	
	@FindBy(linkText = "Accessibility")
	private WebElement accessibilityLink;
	
	@FindBy(xpath = "//a[contains(text(),'Open Government Licence')]")
	private WebElement openGovernmentLicenceLink;
	
	@FindBy(linkText = "© Crown copyright")
	private WebElement crownCopyrightLink;
	
	public HomePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public HomePage navigateToUrl() {
		ScenarioContext.lastDriver.get(PropertiesUtil.getConfigPropertyValue("par_url"));
		return PageFactory.initElements(driver, HomePage.class);
	}
	
	public LoginPage selectLogin() {
		signinButton.click();
		return PageFactory.initElements(driver, LoginPage.class);
	}
	
	public LocalRegulationPrimaryAuthorityPage selectReadMoreAboutPrimaryAuthorityLink() {
		readMorePrimaryAuthorityLink.click();
		return PageFactory.initElements(driver, LocalRegulationPrimaryAuthorityPage.class);
	}
	
	public PrimaryAuthorityDocumentsPage selectAccessToolsAndTemplatesLink() {
		toolsAndTemplatesResourcesLink.click();
		return PageFactory.initElements(driver, PrimaryAuthorityDocumentsPage.class);
	}
	
	public PartnershipSearchPage selectPartnershipSearchLink() {
		searchPartnershipsResourcesLink.click();
		return PageFactory.initElements(driver, PartnershipSearchPage.class);
	}
	
	public TermsAndConditionsPage selectTermsAndConditionsLink() {
		termsAndConditionsLink.click();
		return PageFactory.initElements(driver, TermsAndConditionsPage.class);
	}
	
	public CookiesPage selectCookiesFooterLink() {
		cookiesFooterLink.click();
		return PageFactory.initElements(driver, CookiesPage.class);
	}
	
	public OPSSPrivacyNoticePage selectPrivacyLink() {
		privacyLink.click();
		return PageFactory.initElements(driver, OPSSPrivacyNoticePage.class);
	}
	
	public AccessibilityStatementPage selectAccessibilityLink() {
		accessibilityLink.click();
		return PageFactory.initElements(driver, AccessibilityStatementPage.class);
	}
	
	public OpenGovernmentLicencePage selectOpenGovernmentLicenceLink() {
		openGovernmentLicenceLink.click();
		return PageFactory.initElements(driver, OpenGovernmentLicencePage.class);
	}
	
	public CrownCopyrightPage selectCrownCopyrightLink() {
		crownCopyrightLink.click();
		return PageFactory.initElements(driver, CrownCopyrightPage.class);
	}
}
