package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class UpdateUserConfirmationPage extends BasePageObject {
	public UpdateUserConfirmationPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	@FindBy(linkText = "Done")
	private WebElement doneBtn;
	
	public DashboardPage selectDoneButton() {
		doneBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}