package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class ChoosePersonToAddPage extends BasePageObject{
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	private String personsNameLocator = "//label/div[contains(normalize-space(), '?')]/../../input";
	
	public ChoosePersonToAddPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void choosePerson() {
		WebElement radio = driver.findElement(By.xpath(personsNameLocator.replace("?", DataStore.getSavedValue(UsableValues.PERSON_FULLNAME_TITLE))));
		
		if(radio != null) {
			radio.click();
		}
	}
	
	public UserMembershipPage clickContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, UserMembershipPage.class);
	}
}
