<!-- Edit Background Modal -->
<div class="modal" id="editBackgroundModal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Edit Company Background</h2>
        <form method="POST" action="">
            <textarea name="background_text" placeholder="Enter company background..."><?php echo $background ? htmlspecialchars($background['background_text']) : ''; ?></textarea>
            <button type="submit" name="update_background">Save Changes</button>
        </form>
    </div>
</div>

<!-- Edit Culture Modal -->
<div class="modal" id="editCultureModal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Edit Company Culture & Benefits</h2>
        <form method="POST" action="">
            <h3>Company Culture</h3>
            <textarea name="culture_text" placeholder="Describe your company culture..."><?php echo $culture ? htmlspecialchars($culture['culture_text']) : ''; ?></textarea>
            <h3>Benefits and Perks</h3>
            <textarea name="benefits_text" placeholder="List company benefits and perks..."><?php echo $culture ? htmlspecialchars($culture['benefits_text']) : ''; ?></textarea>
            <button type="submit" name="update_culture">Save Changes</button>
        </form>
    </div>
</div>

<!-- Upload Images Modal -->
<div class="modal" id="uploadModal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Upload Images</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" id="imageType" name="image_type" value="">
            <input type="file" name="gallery_images[]" accept="image/*" multiple required>
            <button type="submit" name="upload_images">Upload Images</button>
        </form>
    </div>
</div>

<!-- Add Job Modal -->
<div class="modal" id="jobModal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Add New Job</h2>
        <form method="POST" action="">
            <input type="text" name="job_title" placeholder="Job Title" required>
            <textarea name="job_description" placeholder="Job Description" required></textarea>
            <textarea name="requirements" placeholder="Job Requirements" required></textarea>
            <input type="text" name="salary_range" placeholder="Salary Range" required>
            <input type="text" name="location" placeholder="Job Location" required>
            <select name="employment_type" required>
                <option value="">Select Employment Type</option>
                <option value="Full-time">Full-time</option>
                <option value="Part-time">Part-time</option>
                <option value="Contract">Contract</option>
                <option value="Internship">Internship</option>
            </select>
            <button type="submit" name="add_job">Add Job</button>
        </form>
    </div>
</div>