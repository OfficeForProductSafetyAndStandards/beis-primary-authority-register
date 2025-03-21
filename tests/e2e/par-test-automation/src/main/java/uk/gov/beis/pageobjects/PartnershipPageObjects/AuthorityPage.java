package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class AuthorityPage extends BasePageObject {

	@FindBy(id = "edit-next")
	private WebElement continueBtn;

	private String authorityLocator = "//label[contains(text(),'?')]";
	
	public AuthorityPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void selectAuthority(String auth) {
		WebElement authority = driver.findElement(By.xpath(authorityLocator.replace("?", auth)));
		
		DataStore.saveValue(UsableValues.AUTHORITY_NAME, authority.getText());
		
		authority.click();
	}
	
	public void selectContinueButton() {
		continueBtn.click();
	}
}
