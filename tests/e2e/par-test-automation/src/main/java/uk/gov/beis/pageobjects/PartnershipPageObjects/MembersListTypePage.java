package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class MembersListTypePage extends BasePageObject {

	@FindBy(id = "edit-type-internal")
	private WebElement internalListRadial;

	@FindBy(id = "edit-type-external")
	private WebElement externalListByLinkRadial;

	@FindBy(id = "edit-type-request")
	private WebElement externalListOnRequestRadial;

	@FindBy(id = "edit-next")
	private WebElement continueBtn;

	public MembersListTypePage() throws ClassNotFoundException, IOException {
		super();
	}

	public void selectMemberListType(String type) {
		switch(type) {
		case "internal":
			internalListRadial.click();
			break;
		case "externalLink":
			externalListByLinkRadial.click();
			break;
		case "externalRequest":
			externalListOnRequestRadial.click();
			break;
		}
	}

	public void clickContinueButton() {
        waitForElementToBeVisible(By.id("edit-next"), 2000);
        continueBtn.click();
        waitForPageLoad();
	}
}
