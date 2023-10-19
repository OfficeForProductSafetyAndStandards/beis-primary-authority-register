package uk.gov.beis.pageobjects.AdvicePageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class AdviceRemovalPage extends BasePageObject {
	
	@FindBy(id = "edit-remove-reason")
	private WebElement removalReasonBox;
	
	@FindBy(id = "edit-next")
	private WebElement removeBtn;
	
	public AdviceRemovalPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public AdviceNoticeSearchPage enterReasonForRemoval(String reason) {
		removalReasonBox.clear();
		removalReasonBox.sendKeys(reason);
		
		removeBtn.click();
		return PageFactory.initElements(driver, AdviceNoticeSearchPage.class);
	}
}
