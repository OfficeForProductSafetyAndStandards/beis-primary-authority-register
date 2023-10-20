package uk.gov.beis.pageobjects.EnforcementNoticePageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.DuplicateClasses.RemoveEnforcementConfirmationPage;

public class RemoveEnforcementPage extends BasePageObject {
	
	@FindBy(id = "edit-reason-description")
	private WebElement descriptionBox;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;

	private String removalReasonLocator = "//label[contains(text(),'?')]";
	
	public RemoveEnforcementPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void selectReasonForRemoval(String reason) {
		driver.findElement(By.xpath(removalReasonLocator.replace("?", reason))).click();
	}
	
	public void enterReasonForRemoval(String reason) {
		descriptionBox.clear();
		descriptionBox.sendKeys(reason);
	}
	
	public RemoveEnforcementConfirmationPage clickContinue() {
		continueBtn.click();
		return PageFactory.initElements(driver, RemoveEnforcementConfirmationPage.class);
	}
}
