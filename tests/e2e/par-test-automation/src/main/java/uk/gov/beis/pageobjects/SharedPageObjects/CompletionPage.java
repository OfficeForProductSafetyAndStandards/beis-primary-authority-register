package uk.gov.beis.pageobjects.SharedPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipAdvancedSearchPage;

public class CompletionPage extends BasePageObject {

	@FindBy(id = "edit-done")
	private WebElement doneBtn;
	
	@FindBy(linkText = "Done")
	private WebElement doneLink;
	
	public CompletionPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public PartnershipAdvancedSearchPage clickDoneForPartnership() {
		doneBtn.click();
		return PageFactory.initElements(driver, PartnershipAdvancedSearchPage.class);
	}
	
	public void clickDoneForInvitation() {
		doneLink.click();
	}
}
