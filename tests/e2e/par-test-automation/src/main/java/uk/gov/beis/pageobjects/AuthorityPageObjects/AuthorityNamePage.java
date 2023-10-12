package uk.gov.beis.pageobjects.AuthorityPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class AuthorityNamePage extends BasePageObject {

	@FindBy(id = "edit-name")
	private WebElement authorityName;

	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	public AuthorityNamePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public AuthorityTypePage enterAuthorityName(String name) {
		authorityName.clear();
		authorityName.sendKeys(name);
		
		continueBtn.click();
		return PageFactory.initElements(driver, AuthorityTypePage.class);
	}
	
	public AuthorityTypePage editAuthorityName(String name) {
		authorityName.clear();
		authorityName.sendKeys(name);
		
		saveBtn.click();
		return PageFactory.initElements(driver, AuthorityTypePage.class);
	}
}
