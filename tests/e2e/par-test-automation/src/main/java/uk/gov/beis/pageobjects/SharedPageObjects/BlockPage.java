package uk.gov.beis.pageobjects.SharedPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.UserManagement.UserProfilePage;

public class BlockPage extends BasePageObject {
	
	@FindBy(id = "edit-next")
	private WebElement blockBtn;
	
	public BlockPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public UserProfilePage goToUserProfilePage() {
		blockBtn.click();
		return PageFactory.initElements(driver, UserProfilePage.class);
	}
}
