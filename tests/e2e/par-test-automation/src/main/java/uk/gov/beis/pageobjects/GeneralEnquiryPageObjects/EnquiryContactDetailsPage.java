package uk.gov.beis.pageobjects.GeneralEnquiryPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class EnquiryContactDetailsPage extends BasePageObject{
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public EnquiryContactDetailsPage() throws ClassNotFoundException, IOException {
		super();
	}

	public RequestEnquiryPage proceed() {
		continueBtn.click();
		return PageFactory.initElements(driver, RequestEnquiryPage.class);
	}
}