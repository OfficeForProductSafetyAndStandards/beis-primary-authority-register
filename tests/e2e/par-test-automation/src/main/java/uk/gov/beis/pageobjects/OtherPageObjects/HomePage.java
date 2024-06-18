package uk.gov.beis.pageobjects.OtherPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.helper.PropertiesUtil;
import uk.gov.beis.helper.ScenarioContext;
import uk.gov.beis.pageobjects.BasePageObject;

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
	
	public void navigateToUrl() {
		ScenarioContext.lastDriver.get(PropertiesUtil.getConfigPropertyValue("par_url"));
	}
	
	public void selectLogin() {
		signinButton.click();
	}
	
	public void selectReadMoreAboutPrimaryAuthorityLink() {
		readMorePrimaryAuthorityLink.click();
	}
	
	public void selectAccessToolsAndTemplatesLink() {
		toolsAndTemplatesResourcesLink.click();
	}
	
	public void selectPartnershipSearchLink() {
		searchPartnershipsResourcesLink.click();
	}
	
	public void selectTermsAndConditionsLink() {
		termsAndConditionsLink.click();
	}
	
	public void selectCookiesFooterLink() {
		cookiesFooterLink.click();
	}
	
	public void selectPrivacyLink() {
		privacyLink.click();
	}
	
	public void selectAccessibilityLink() {
		accessibilityLink.click();
	}
	
	public void selectOpenGovernmentLicenceLink() {
		openGovernmentLicenceLink.click();
	}
	
	public void selectCrownCopyrightLink() {
		crownCopyrightLink.click();
	}
}
