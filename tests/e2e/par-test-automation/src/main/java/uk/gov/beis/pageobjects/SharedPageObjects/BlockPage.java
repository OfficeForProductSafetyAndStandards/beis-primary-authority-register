package uk.gov.beis.pageobjects.SharedPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class BlockPage extends BasePageObject {

	@FindBy(id = "edit-next")
	private WebElement blockBtn;

	public BlockPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void clickBlockButton() {
        waitForElementToBeClickable(By.id("edit-next"), 2000);
        blockBtn.click();
	}
}
