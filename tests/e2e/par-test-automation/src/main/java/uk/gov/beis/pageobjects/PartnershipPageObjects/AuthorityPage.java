package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.OtherPageObjects.HomePage;
import uk.gov.beis.utility.DataStore;

public class AuthorityPage extends BasePageObject {

	@FindBy(xpath = "//span[@class='govuk-header__logotype-text']")
	private WebElement pageHeader;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;

	private String authorityLocator = "//label[contains(text(),'?')]";
	
	public AuthorityPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public PartnershipTypePage selectAuthority(String auth) {
		WebElement authority = driver.findElement(By.xpath(authorityLocator.replace("?", auth)));
		
		DataStore.saveValue(UsableValues.AUTHORITY_NAME, authority.getText());
		
		authority.click();
		
		continueBtn.click();
		return PageFactory.initElements(driver, PartnershipTypePage.class);
	}
	
	public HomePage selectPageHeader() {
		pageHeader.click();
		return PageFactory.initElements(driver, HomePage.class);
	}
}
