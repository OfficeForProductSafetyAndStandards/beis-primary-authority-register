package uk.gov.beis.pageobjects.AuthorityPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class AuthorityTypePage extends BasePageObject {

	@FindBy(id = "edit-next")
	private WebElement continueBtn;

	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	private String locator = "//label[contains(text(),'?')]";
	
	public AuthorityTypePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public AuthorityAddressDetailsPage selectAuthorityType(String type) {
		WebElement link = driver.findElement(By.xpath(locator.replace("?", type)));
		link.click();
		
		continueBtn.click();
		return PageFactory.initElements(driver, AuthorityAddressDetailsPage.class);
	}
	
	public AuthorityAddressDetailsPage editAuthorityType(String type) {
		WebElement link = driver.findElement(By.xpath(locator.replace("?", type)));
		link.click();
		
		saveBtn.click();
		return PageFactory.initElements(driver, AuthorityAddressDetailsPage.class);
	}
}