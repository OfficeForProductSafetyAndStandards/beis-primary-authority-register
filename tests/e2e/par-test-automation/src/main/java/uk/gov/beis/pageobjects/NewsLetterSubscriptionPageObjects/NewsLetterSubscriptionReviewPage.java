package uk.gov.beis.pageobjects.NewsLetterSubscriptionPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class NewsLetterSubscriptionReviewPage extends BasePageObject {

	@FindBy(id = "edit-save")
	private WebElement updateListBtn;

	public NewsLetterSubscriptionReviewPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void clickUpdateListButton() {
        waitForElementToBeClickable(By.id("edit-save"), 3000);
    	updateListBtn.click();
        waitForPageLoad();
	}
}
