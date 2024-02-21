package uk.gov.beis.pageobjects.SharedPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class ChooseAnInspectionPlanPage extends BasePageObject {
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public ChooseAnInspectionPlanPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void selectContinueButton() {
		continueBtn.click();
	}
}
