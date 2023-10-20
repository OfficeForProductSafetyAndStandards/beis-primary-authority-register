package uk.gov.beis.pageobjects.AdvicePageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.DuplicateClasses.InspectionPlanExpirationPage;

public class AdviceNoticeDetailsPage extends BasePageObject {

	@FindBy(id = "edit-advice-title")
	private WebElement title;
	
	@FindBy(id = "edit-notes")
	private WebElement descriptionBox;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;

	private String advicetype = "//label[contains(text(),'?')]";
	private String regfunc = "//label[contains(text(),'?')]";
	
	public AdviceNoticeDetailsPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void enterTitle(String value) {
		title.clear();
		title.sendKeys(value);
	}
	
	public void selectAdviceType(String type) {
		WebElement adviceType = driver.findElement(By.xpath(advicetype.replace("?", type)));
		
		if(!adviceType.isSelected()) {
			adviceType.click();
		}
	}

	public void selectRegFunc(String func) {
		WebElement regulatoryFunctions = driver.findElement(By.xpath(regfunc.replace("?", func)));
		
		if(!regulatoryFunctions.isSelected()) {
			regulatoryFunctions.click();
		}
	}

	public void enterDescription(String description) throws Throwable {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
	}

	public InspectionPlanExpirationPage save() {
		saveBtn.click();
		return PageFactory.initElements(driver, InspectionPlanExpirationPage.class);
	}

	public AdviceNoticeSearchPage clickSave() {
		saveBtn.click();
		return PageFactory.initElements(driver, AdviceNoticeSearchPage.class);
	}
}
