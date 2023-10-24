package uk.gov.beis.pageobjects.NewsLetterSubscriptionPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class NewsLetterSubscriptionReviewPage extends BasePageObject {
	
	@FindBy(id = "edit-save")
	private WebElement updateListBtn;
	
	public NewsLetterSubscriptionReviewPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public NewsLetterSubscriptionPage clickUpdateListButton() {
		updateListBtn.click();
		return PageFactory.initElements(driver, NewsLetterSubscriptionPage.class);
	}
}
