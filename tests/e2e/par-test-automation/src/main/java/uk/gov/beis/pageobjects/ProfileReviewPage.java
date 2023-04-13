package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class ProfileReviewPage extends BasePageObject {
	public ProfileReviewPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//h1[@class='heading-xlarge']")
	private WebElement pageHeader;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	@FindBy(id = "edit-cancel")
	private WebElement cancelBtn;
	
	public String getHeaderText() {
		return pageHeader.getText();
	}
	
	public PersonCompletionConfirmationPage savePersonCreation() {
		saveBtn.click();
		return PageFactory.initElements(driver, PersonCompletionConfirmationPage.class);
	}
	
	public UpdateUserConfirmationPage saveContactUpdate() {
		saveBtn.click();
		return PageFactory.initElements(driver, UpdateUserConfirmationPage.class);
	}
	
	public DashboardPage clickCancelButton() {
		cancelBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}
