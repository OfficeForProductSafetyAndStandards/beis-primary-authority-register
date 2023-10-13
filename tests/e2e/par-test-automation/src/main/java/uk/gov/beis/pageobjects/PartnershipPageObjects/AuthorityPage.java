package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.HomePage;

public class AuthorityPage extends BasePageObject {

	public AuthorityPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//span[@class='govuk-header__logotype-text']")
	private WebElement pageHeader;
	
	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	private String authority = "//label[contains(text(),'?')]";

	public PartnershipTypePage selectAuthority(String auth) {
		WebElement link = driver.findElement(By.xpath(authority.replace("?", auth)));
		link.click();
		continueBtn.click();
		return PageFactory.initElements(driver, PartnershipTypePage.class);
	}
	
	public HomePage selectPageHeader() {
		pageHeader.click();
		return PageFactory.initElements(driver, HomePage.class);
	}
}
