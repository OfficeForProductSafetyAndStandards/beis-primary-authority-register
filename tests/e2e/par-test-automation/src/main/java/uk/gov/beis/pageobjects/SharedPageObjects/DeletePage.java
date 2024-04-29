package uk.gov.beis.pageobjects.SharedPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class DeletePage extends BasePageObject {
	
	@FindBy(id = "edit-deletion-reason")
	private WebElement deletionReasonTextArea;

	@FindBy(id = "edit-next")
	private WebElement deleteBtn;
	
	public DeletePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void enterReasonForDeletion(String reason) {
		deletionReasonTextArea.clear();
		deletionReasonTextArea.sendKeys(reason);
	}
	
	public CompletionPage clickDeleteForPartnership() {
		deleteBtn.click();
		return PageFactory.initElements(driver, CompletionPage.class);
	}
}
