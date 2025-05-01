package uk.gov.beis.pageobjects.AdvicePageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class AdviceNoticeDetailsPage extends BasePageObject {

	@FindBy(id = "edit-advice-title")
	private WebElement title;

	@FindBy(id = "edit-notes")
	private WebElement descriptionBox;

	@FindBy(id = "edit-save")
	private WebElement saveBtn;

	private String advicetype = "//label[contains(text(),'?')]/preceding-sibling::input";
	private String regfunc = "//label[contains(text(),'?')]/preceding-sibling::input";

	public AdviceNoticeDetailsPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void enterTitle(String value) {
        waitForElementToBeVisible(By.id("edit-advice-title"), 2000);
		title.clear();
		title.sendKeys(value);
	}

	public void selectAdviceType(String type) {
        //waitForElementToBeVisible(By.xpath(advicetype.replace("?", type)), 2000);
		WebElement adviceType = driver.findElement(By.xpath(advicetype.replace("?", type)));

		if(!adviceType.isSelected()) {
			adviceType.click();
            waitForPageLoad();
		}
	}

	public void selectRegulatoryFunction(String func) {
        //waitForElementToBeVisible(By.xpath(regfunc.replace("?", func)), 2000);
		WebElement regulatoryFunctions = driver.findElement(By.xpath(regfunc.replace("?", func)));

		if(!regulatoryFunctions.isSelected()) {
			regulatoryFunctions.click();
            waitForPageLoad();
		}
	}

	public void enterDescription(String description) {
        waitForElementToBeVisible(By.id("edit-notes"), 2000);
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
	}

	public void clearAllFields() {
		title.clear();

        //waitForElementToBeVisible(By.xpath("//input[@type='checkbox']"), 2000);
		WebElement regulatoryFunctions = driver.findElement(By.xpath("//input[@type='checkbox']"));

		if(regulatoryFunctions.isSelected()) {
			regulatoryFunctions.click();
		}

		descriptionBox.clear();
        waitForPageLoad();
	}

	public void selectSaveButton() {

        waitForElementToBeVisible(By.id("edit-save"), 2000);
        saveBtn.click();
        waitForPageLoad();
	}
}
