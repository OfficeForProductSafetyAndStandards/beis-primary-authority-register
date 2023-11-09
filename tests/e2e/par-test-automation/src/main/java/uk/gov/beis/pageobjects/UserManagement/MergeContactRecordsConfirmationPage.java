package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class MergeContactRecordsConfirmationPage extends BasePageObject {
	
	@FindBy(id = "edit-next")
	private WebElement mergeBtn;
	
	public MergeContactRecordsConfirmationPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public UserProfilePage clickMerge() {
		mergeBtn.click();
		return PageFactory.initElements(driver, UserProfilePage.class);
	}
}
